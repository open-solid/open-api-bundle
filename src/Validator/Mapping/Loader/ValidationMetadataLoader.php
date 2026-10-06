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

namespace OpenSolid\OpenApiBundle\Validator\Mapping\Loader;

use OpenApi\Undefined;
use OpenSolid\OpenApiBundle\Attribute\Property;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class ValidationMetadataLoader implements ValidatorMetadataLoaderInterface
{
    public function load(ClassMetadata $metadata, \ReflectionProperty $reflectionProperty, Property $property): bool
    {
        $groups = $property->groups;
        $loaded = false;

        if (!Undefined::isDefault($property->minLength) || !Undefined::isDefault($property->maxLength)) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\Length(
                min: Undefined::isDefault($property->minLength) ? null : $property->minLength,
                max: Undefined::isDefault($property->maxLength) ? null : $property->maxLength,
                groups: $groups,
            ));
            $loaded = true;
        }

        if (!Undefined::isDefault($property->minItems) || !Undefined::isDefault($property->maxItems)) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\Count(
                min: Undefined::isDefault($property->minItems) ? null : $property->minItems,
                max: Undefined::isDefault($property->maxItems) ? null : $property->maxItems,
                groups: $groups,
            ));
            $loaded = true;
        }

        // OpenAPI 3.0 flags "minimum" as exclusive, OpenAPI 3.1 gives the exclusive bound itself
        if (\is_int($property->exclusiveMinimum) || \is_float($property->exclusiveMinimum)) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\GreaterThan(value: $property->exclusiveMinimum, groups: $groups));
            $loaded = true;
        }

        if (!Undefined::isDefault($property->minimum)) {
            $constraint = true === $property->exclusiveMinimum
                ? new Assert\GreaterThan(value: $property->minimum, groups: $groups)
                : new Assert\GreaterThanOrEqual(value: $property->minimum, groups: $groups);
            $metadata->addPropertyConstraint($reflectionProperty->name, $constraint);
            $loaded = true;
        }

        if (\is_int($property->exclusiveMaximum) || \is_float($property->exclusiveMaximum)) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\LessThan(value: $property->exclusiveMaximum, groups: $groups));
            $loaded = true;
        }

        if (!Undefined::isDefault($property->maximum)) {
            $constraint = true === $property->exclusiveMaximum
                ? new Assert\LessThan(value: $property->maximum, groups: $groups)
                : new Assert\LessThanOrEqual(value: $property->maximum, groups: $groups);
            $metadata->addPropertyConstraint($reflectionProperty->name, $constraint);
            $loaded = true;
        }

        if (!Undefined::isDefault($property->pattern)) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\Regex(pattern: $property->pattern, groups: $groups));
            $loaded = true;
        }

        if (!Undefined::isDefault($property->uniqueItems)) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\Unique(groups: $groups));
            $loaded = true;
        }

        if (!Undefined::isDefault($property->enum)) {
            $enum = $property->enum;
            $choices = [];

            if (is_string($enum) && is_subclass_of($enum, \UnitEnum::class)) {
                $enum = $enum::cases();
            }

            if (is_array($enum)) {
                foreach ($enum as $case) {
                    if ($case instanceof \BackedEnum) {
                        $case = $case->value;
                    } elseif ($case instanceof \UnitEnum) {
                        $case = $case->name;
                    }
                    $choices[] = $case;
                }
            }

            if ($choices) {
                $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\Choice(choices: $choices, groups: $groups));
                $loaded = true;
            }
        }

        if (!Undefined::isDefault($property->multipleOf)) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\DivisibleBy(value: $property->multipleOf, groups: $groups));
            $loaded = true;
        }

        if (!Undefined::isDefault($property->const)) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\EqualTo(value: $property->const, groups: $groups));
            $loaded = true;
        }

        $type = $reflectionProperty->getType();

        if (!$type instanceof \ReflectionType) {
            return $loaded;
        }

        if (!$type->allowsNull()) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\NotNull(groups: $groups));
            $loaded = true;
        }

        if (!$type->isBuiltin()) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\Valid(groups: $groups));
            $loaded = true;
        }

        return $loaded;
    }
}
