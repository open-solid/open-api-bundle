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
use OpenApi\Undefined;
use OpenSolid\OpenApiBundle\OpenApi\Constraint\ConstraintSchemaApplier;

/**
 * Infers the "required" fields of the object schemas, and the "default" value of the
 * properties promoted from constructor parameters (swagger-php only reads class properties).
 *
 * A property backed by PHP reflection is required when it is not nullable and has no
 * default value, or when a NotNull / NotBlank constraint asks for it. A property without
 * reflection (e.g. defined in a YAML file) is required when it belongs to a write-only
 * schema and is not nullable.
 */
readonly class AugmentSchemas implements ProcessorInterface
{
    public function __construct(
        private ?ConstraintSchemaApplier $applier = null,
    ) {
    }

    public function __invoke(Analysis $analysis): void
    {
        /** @var OA\Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(OA\Schema::class);

        foreach ($schemas as $schema) {
            if (!Undefined::isDefault($schema->required) || Undefined::isDefault($schema->properties)) {
                continue;
            }

            $required = [];
            foreach ($schema->properties as $property) {
                $this->augmentPromotedDefault($property);

                if ($this->isRequired($schema, $property)) {
                    $required[] = Undefined::isDefault($property->property) ? $property->_context->property : $property->property;
                }
            }

            if ([] !== $required) {
                $schema->required = $required;
            }
        }
    }

    private function isRequired(OA\Schema $schema, OA\Property $property): bool
    {
        $reflector = ConstraintSchemaApplier::resolveReflector($property->_context->reflector ?? null);

        if (!$reflector instanceof \ReflectionProperty) {
            return true === $schema->writeOnly && true !== $property->nullable;
        }

        $type = $reflector->getType();
        $isNullable = null === $type || $type->allowsNull();

        // A NotNull or NotBlank constraint makes the field mandatory in the payload
        // even when the PHP type is nullable or carries a default.
        if (true === $this->applier?->isRequired($reflector)) {
            if ($isNullable) {
                $this->denyNull($property);
            }

            return true;
        }

        return !$isNullable && !$reflector->hasDefaultValue() && null === $this->promotedParameterWithDefault($reflector);
    }

    private function augmentPromotedDefault(OA\Property $property): void
    {
        $reflector = ConstraintSchemaApplier::resolveReflector($property->_context->reflector ?? null);

        if (!$reflector instanceof \ReflectionProperty || !Undefined::isDefault($property->default)) {
            return;
        }

        if (null !== ($default = $this->promotedParameterWithDefault($reflector)?->getDefaultValue())) {
            $property->default = $default instanceof \BackedEnum ? $default->value : ($default instanceof \UnitEnum ? $default->name : $default);
        }
    }

    /**
     * A promoted property never reports a default value; its constructor parameter does.
     */
    private function promotedParameterWithDefault(\ReflectionProperty $reflector): ?\ReflectionParameter
    {
        if (!$reflector->isPromoted()) {
            return null;
        }

        $parameter = new \ReflectionParameter([$reflector->getDeclaringClass()->getName(), '__construct'], $reflector->getName());

        return $parameter->isDefaultValueAvailable() ? $parameter : null;
    }

    /**
     * Keeps a mandatory field from also advertising null.
     *
     * The constraint rejects null, so a schema that still allows it would tell a client to
     * send a value the request would then reject. "false" rather than undefined: the
     * swagger-php type resolver runs later and only fills a nullable flag left undefined.
     */
    private function denyNull(OA\Schema $property): void
    {
        $property->nullable = false;

        if (!\is_array($property->type)) {
            return;
        }

        $types = array_values(array_filter($property->type, static fn (string $type): bool => 'null' !== $type));

        $property->type = match (\count($types)) {
            0 => Undefined::UNDEFINED,
            1 => $types[0],
            default => $types,
        };
    }
}
