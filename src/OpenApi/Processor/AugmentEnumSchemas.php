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
use OpenApi\GeneratorAwareInterface;
use OpenApi\GeneratorAwareTrait;
use OpenApi\Undefined;
use OpenSolid\OpenApiBundle\OpenApi\Constraint\ConstraintSchemaApplier;

/**
 * Infers `type` and `enum` for schemas and parameters backed by a PHP enum.
 *
 * swagger-php turns an enum-typed property into a $ref, but only when the enum class
 * carries #[OA\Schema] and lives inside the scanned paths. A plain domain enum is left
 * without a type. This processor inlines its cases instead, so that
 * `public ?ListingFeeAllocation $allocation` renders as:
 *
 *     type: ['string', 'null']
 *     enum: [...]
 *
 * An enum that already resolved to a $ref keeps it.
 *
 * Finally it cleans up a `type` that still holds a raw PHP class name, which is how the
 * swagger-php type resolver reports the value type of a `list<T>`.
 */
class AugmentEnumSchemas implements ProcessorInterface, GeneratorAwareInterface
{
    use GeneratorAwareTrait;

    public function __invoke(Analysis $analysis): void
    {
        /** @var OA\Parameter[] $parameters */
        $parameters = $analysis->getAnnotationsOfType(OA\Parameter::class);

        // a parameter schema inherits the reflector of its parameter, so it is handled below
        $parameterSchemas = new \SplObjectStorage();
        foreach ($parameters as $parameter) {
            if (!Undefined::isDefault($parameter->schema)) {
                $parameterSchemas->offsetSet($parameter->schema);
            }
        }

        /** @var OA\Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(OA\Schema::class);

        foreach ($schemas as $schema) {
            if ($parameterSchemas->offsetExists($schema)) {
                continue;
            }

            $this->inferFromReflection($schema, $schema->_context->reflector ?? null, true);
            $this->resolveClassStringType($schema);
        }

        foreach ($parameters as $parameter) {
            if (Undefined::isDefault($parameter->schema)) {
                continue;
            }

            $this->inferFromReflection($parameter->schema, $parameter->_context->reflector ?? null, false);
            $this->resolveClassStringType($parameter->schema);
        }
    }

    /**
     * @param bool $nullable whether a nullable PHP type makes the schema nullable; a query string
     *                       carries no real null, so a parameter schema never is
     */
    private function inferFromReflection(OA\Schema $schema, ?\Reflector $reflector, bool $nullable): void
    {
        if (!Undefined::isDefault($schema->type)
            || !Undefined::isDefault($schema->ref)
            || !Undefined::isDefault($schema->enum)
        ) {
            return;
        }

        $reflector = ConstraintSchemaApplier::resolveReflector($reflector);

        if (null === $reflector) {
            return;
        }

        $type = $reflector->getType();

        if (!$type instanceof \ReflectionNamedType || $type->isBuiltin() || !enum_exists($type->getName())) {
            return;
        }

        $this->applyEnum($schema, new \ReflectionEnum($type->getName()));

        if ($nullable && $type->allowsNull()) {
            if (Undefined::isDefault($schema->nullable)) {
                $schema->nullable = true;
            }

            // a JSON Schema "enum" rejects any value it does not list, null included
            if (true === $schema->nullable) {
                $schema->enum[] = null;
            }
        }
    }

    /**
     * Rewrites a `type` that still holds a raw PHP class name.
     *
     * The type resolver writes the value type of a collection into `items.type` and maps
     * only the top-level `type` back to a spec type, so `list<SomeEnum>` leaves the enum
     * class name behind. An enum is inlined the same way a scalar enum property is; any
     * other class name is dropped, because a PHP class name is no OpenAPI type.
     */
    private function resolveClassStringType(OA\Schema $schema): void
    {
        if ($schema->items instanceof OA\Items) {
            $this->resolveClassStringType($schema->items);
        }

        $type = $schema->type;

        if (Undefined::isDefault($type) || !\is_string($type) || 'null' === strtolower($type)) {
            return;
        }

        if ($this->generator->getTypeResolver()->mapNativeType($schema, $type)) {
            return;
        }

        $class = ltrim($type, '\\');

        if (enum_exists($class)) {
            $this->applyEnum($schema, new \ReflectionEnum($class));

            return;
        }

        // Any other class name is dropped: a schema that never resolved to a $ref carries
        // no type a client can read. A type that names no class is left alone, because it
        // can be a hand-written one.
        if (class_exists($class) || interface_exists($class)) {
            $schema->type = Undefined::UNDEFINED;
        }
    }

    /**
     * Inlines the cases of an enum as `enum`, with the type of its backing value.
     *
     * @param \ReflectionEnum<\UnitEnum> $enum
     */
    private function applyEnum(OA\Schema $schema, \ReflectionEnum $enum): void
    {
        $backingType = $enum->isBacked() ? (string) $enum->getBackingType() : null;

        $schema->enum = array_map(
            static fn (\ReflectionEnumUnitCase $case): string|int => $case instanceof \ReflectionEnumBackedCase
                ? $case->getBackingValue()
                : $case->name,
            $enum->getCases(),
        );

        $schema->type = null === $backingType
            ? 'string'
            : $this->generator->getTypeResolver()->native2spec($backingType);
    }
}
