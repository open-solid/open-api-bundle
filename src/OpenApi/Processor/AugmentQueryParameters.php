<?php

declare(strict_types=1);

/*
 * This file is part of OpenSolid package.
 *
 * (c) Yonel Ceruto <open@yceruto.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenSolid\OpenApiBundle\OpenApi\Processor;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\GeneratorAwareInterface;
use OpenApi\GeneratorAwareTrait;
use OpenApi\Processors\Concerns\DocblockTrait;
use OpenApi\Undefined;

/**
 * Fills gaps left by AugmentParameters for parameters backed by a ReflectionParameter or
 * ReflectionProperty.
 *
 * swagger-php only runs its type resolver for parameters backed by a ReflectionParameter,
 * so a query parameter expanded from a #[MapQueryString] DTO property gets nothing. This
 * processor runs the same resolver for those, giving query parameters the same inference
 * as schema properties: enums, `list<T>` items, docblock generics, date formats and
 * component refs.
 *
 * It also infers the description from the property docblock, the default value and the
 * required flag from the PHP type, and the serialization style of array parameters.
 *
 * Nullability is deliberately not copied onto the parameter schema. A query string never
 * carries a real null, so optionality is expressed by `required: false` alone. This matches
 * what swagger-php does for ReflectionParameter-backed parameters.
 */
class AugmentQueryParameters implements ProcessorInterface, GeneratorAwareInterface
{
    use DocblockTrait;
    use GeneratorAwareTrait;

    /**
     * Schema keywords copied from the inferred type. Mirrors swagger-php's own AugmentParameters.
     */
    private const INFERRED_KEYWORDS = ['type', 'format', 'items', 'enum', 'oneOf', 'allOf', 'anyOf', 'ref'];

    public function __invoke(Analysis $analysis): void
    {
        /** @var OA\Parameter[] $parameters */
        $parameters = $analysis->getAnnotationsOfType(OA\Parameter::class);

        foreach ($parameters as $parameter) {
            $reflector = $parameter->_context->reflector ?? null;

            if (!$reflector instanceof \ReflectionParameter && !$reflector instanceof \ReflectionProperty) {
                continue;
            }

            if (Undefined::isDefault($parameter->schema)) {
                $parameter->schema = new OA\Schema([
                    '_context' => new Context(['generated' => true], $parameter->_context),
                ]);
            }

            $schema = $parameter->schema;
            $type = $reflector->getType();
            $hasDefault = $reflector instanceof \ReflectionParameter
                ? $reflector->isDefaultValueAvailable()
                : $reflector->hasDefaultValue();

            $this->augmentType($analysis, $parameter, $schema, $reflector, $type);
            $this->augmentDescription($parameter, $reflector);

            if (Undefined::isDefault($schema->default) && $hasDefault) {
                $default = $reflector->getDefaultValue();
                if (null !== $default) {
                    $schema->default = $default instanceof \BackedEnum ? $default->value : ($default instanceof \UnitEnum ? $default->name : $default);
                }
            }

            // swagger-php cannot decide this one for a ReflectionParameter: the parameter
            // context is nested, so its type resolver bails out and leaves `required: true`
            // on an empty schema. It never sets it for a ReflectionProperty, so there a
            // `true` is explicit and is kept. A path parameter is always required, whatever
            // the PHP signature says.
            $isOptional = $hasDefault || (null !== $type && $type->allowsNull());

            if ('path' !== $parameter->in
                && (Undefined::isDefault($parameter->required) || ($reflector instanceof \ReflectionParameter && $parameter->required && $isOptional))
            ) {
                $parameter->required = !$isOptional;
            }

            $this->augmentArrayStyle($parameter, $schema);
        }
    }

    private function augmentType(
        Analysis $analysis,
        OA\Parameter $parameter,
        OA\Schema $schema,
        \ReflectionParameter|\ReflectionProperty $reflector,
        ?\ReflectionType $type,
    ): void {
        if (!Undefined::isDefault($schema->type) || !Undefined::isDefault($schema->ref)) {
            return;
        }

        // A throwaway schema keeps the resolver from writing keywords a parameter must not
        // carry, most importantly `nullable`. The context must not be nested: the type
        // resolver bails on a nested context, and the operation guessers mark the
        // parameter context as nested.
        $probe = new OA\Schema([
            '_context' => new Context([
                'generated' => true,
                'nested' => null,
                'reflector' => $reflector,
            ], $parameter->_context),
        ]);

        $this->generator->getTypeResolver()->augmentSchemaType($analysis, $probe);

        foreach (self::INFERRED_KEYWORDS as $keyword) {
            if (!Undefined::isDefault($probe->{$keyword}) && Undefined::isDefault($schema->{$keyword})) {
                $schema->{$keyword} = $probe->{$keyword};
            }
        }
    }

    /**
     * Takes the description from the docblock: the `@var` line or the summary of a property,
     * the `@param` line of a method parameter. This matches what AugmentProperties does for
     * schema properties.
     */
    private function augmentDescription(OA\Parameter $parameter, \ReflectionParameter|\ReflectionProperty $reflector): void
    {
        if (!Undefined::isDefault($parameter->description)) {
            return;
        }

        $description = $reflector instanceof \ReflectionProperty
            ? $this->propertyDescription($reflector)
            : $this->parameterDescription($reflector);

        if (null !== $description && '' !== $description) {
            $parameter->description = $description;
        }
    }

    private function propertyDescription(\ReflectionProperty $reflector): ?string
    {
        $comment = $reflector->getDocComment();

        if (false === $comment) {
            return null;
        }

        $description = $this->parseVarLine($comment)['description'] ?? null;

        if (null === $description || '' === $description) {
            $description = $this->parseDocblock($comment);
        }

        return Undefined::isDefault($description) ? null : trim((string) $description);
    }

    private function parameterDescription(\ReflectionParameter $reflector): ?string
    {
        $comment = $reflector->getDeclaringFunction()->getDocComment();

        if (false === $comment) {
            return null;
        }

        $tags = [];
        $this->parseDocblock($comment, $tags);
        $description = $tags['param'][$reflector->getName()]['description'] ?? null;

        return null === $description ? null : trim((string) $description);
    }

    /**
     * Repeated query parameters (?tags=a&tags=b) need form style with explode.
     */
    private function augmentArrayStyle(OA\Parameter $parameter, OA\Schema $schema): void
    {
        if ('query' !== $parameter->in || 'array' !== $schema->type) {
            return;
        }

        if (Undefined::isDefault($parameter->style)) {
            $parameter->style = 'form';
        }

        if (Undefined::isDefault($parameter->explode)) {
            $parameter->explode = true;
        }
    }
}
