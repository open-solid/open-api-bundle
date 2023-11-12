<?php

namespace Yceruto\OpenApiBundle\Validator\Mapping\Loader;

use OpenApi\Generator;
use ReflectionProperty;
use ReflectionType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use UnitEnum;
use Yceruto\OpenApiBundle\Attribute\Property;

class ValidationMetadataLoader implements ValidatorMetadataLoaderInterface
{
    public function load(ClassMetadata $metadata, ReflectionProperty $reflectionProperty, Property $property): bool
    {
        $groups = $property->groups;
        $loaded = false;

        if (!Generator::isDefault($property->minLength) || !Generator::isDefault($property->maxLength)) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\Length(
                min: Generator::isDefault($property->minLength) ? null : $property->minLength,
                max: Generator::isDefault($property->maxLength) ? null : $property->maxLength,
                groups: $groups,
            ));
            $loaded = true;
        }

        if (!Generator::isDefault($property->minItems) || !Generator::isDefault($property->maxItems)) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\Count(
                min: Generator::isDefault($property->minItems) ? null : $property->minItems,
                max: Generator::isDefault($property->maxItems) ? null : $property->maxItems,
                groups: $groups,
            ));
            $loaded = true;
        }

        if (!Generator::isDefault($property->minimum)) {
            $constraint = Generator::isDefault($property->exclusiveMinimum)
                ? new Assert\GreaterThanOrEqual(value: $property->minimum, groups: $groups)
                : new Assert\GreaterThan(value: $property->minimum, groups: $groups);
            $metadata->addPropertyConstraint($reflectionProperty->name, $constraint);
            $loaded = true;
        }

        if (!Generator::isDefault($property->maximum)) {
            $constraint = Generator::isDefault($property->exclusiveMaximum)
                ? new Assert\LessThanOrEqual(value: $property->maximum, groups: $groups)
                : new Assert\LessThan(value: $property->maximum, groups: $groups);
            $metadata->addPropertyConstraint($reflectionProperty->name, $constraint);
            $loaded = true;
        }

        if (!Generator::isDefault($property->pattern)) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\Regex(pattern: $property->pattern, groups: $groups));
            $loaded = true;
        }

        if (!Generator::isDefault($property->uniqueItems)) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\Unique(groups: $groups));
            $loaded = true;
        }

        if (!Generator::isDefault($property->enum)) {
            $enum = $property->enum;
            $choices = [];

            if (is_string($enum) && is_subclass_of($enum, UnitEnum::class)) {
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

        if (!Generator::isDefault($property->multipleOf)) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\DivisibleBy(value: $property->multipleOf, groups: $groups));
            $loaded = true;
        }

        if (!Generator::isDefault($property->const)) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\EqualTo(value: $property->const, groups: $groups));
            $loaded = true;
        }

        $type = $reflectionProperty->getType();

        if (!$type instanceof ReflectionType) {
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
