<?php

namespace Yceruto\OpenApiBundle\Mapping\Loader;

use OpenApi\Generator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\LoaderInterface;
use UnitEnum;
use Yceruto\OpenApiBundle\Attributes\Property;

class OpenApiValidatorAttributeLoader implements LoaderInterface
{
    public function loadClassMetadata(ClassMetadata $metadata): bool
    {
        $reflClass = $metadata->getReflectionClass();
        $loaded = false;

        foreach ($reflClass->getProperties() as $property) {
            foreach ($this->getAttributes($property) as $attribute) {
                $groups = $attribute->groups;

                if ('uuid' === $attribute->format) {
                    $metadata->addPropertyConstraint($property->name, new Assert\Uuid(groups: $groups));
                    $loaded = true;
                }

                if ('email' === $attribute->format) {
                    $metadata->addPropertyConstraint($property->name, new Assert\Email(groups: $groups));
                    $loaded = true;
                }

                if ('password' === $attribute->format) {
                    $metadata->addPropertyConstraint($property->name, new Assert\PasswordStrength(minScore: Assert\PasswordStrength::STRENGTH_VERY_STRONG, groups: $groups));
                    $loaded = true;
                }

                if ('date' === $attribute->format) {
                    $metadata->addPropertyConstraint($property->name, new Assert\Date(groups: $groups));
                    $loaded = true;
                }

                if ('date-time' === $attribute->format) {
                    $metadata->addPropertyConstraint($property->name, new Assert\DateTime(groups: $groups));
                    $loaded = true;
                }

                if ('currency' === $attribute->format) {
                    $metadata->addPropertyConstraint($property->name, new Assert\Currency(groups: $groups));
                    $loaded = true;
                }

                if (!Generator::isDefault($attribute->minLength) || !Generator::isDefault($attribute->maxLength)) {
                    $metadata->addPropertyConstraint($property->name, new Assert\Length(
                        min: Generator::isDefault($attribute->minLength) ? null : $attribute->minLength,
                        max: Generator::isDefault($attribute->maxLength) ? null : $attribute->maxLength,
                        groups: $groups,
                    ));
                    $loaded = true;
                }

                if (!Generator::isDefault($attribute->minItems) || !Generator::isDefault($attribute->maxItems)) {
                    $metadata->addPropertyConstraint($property->name, new Assert\Count(
                        min: Generator::isDefault($attribute->minItems) ? null : $attribute->minItems,
                        max: Generator::isDefault($attribute->maxItems) ? null : $attribute->maxItems,
                        groups: $groups,
                    ));
                    $loaded = true;
                }

                if (!Generator::isDefault($attribute->minimum)) {
                    $constraint = Generator::isDefault($attribute->exclusiveMinimum)
                        ? new Assert\GreaterThanOrEqual(value: $attribute->minimum, groups: $groups)
                        : new Assert\GreaterThan(value: $attribute->minimum, groups: $groups);
                    $metadata->addPropertyConstraint($property->name, $constraint);
                    $loaded = true;
                }

                if (!Generator::isDefault($attribute->maximum)) {
                    $constraint = Generator::isDefault($attribute->exclusiveMaximum)
                        ? new Assert\LessThanOrEqual(value: $attribute->maximum, groups: $groups)
                        : new Assert\LessThan(value: $attribute->maximum, groups: $groups);
                    $metadata->addPropertyConstraint($property->name, $constraint);
                    $loaded = true;
                }

                if (!Generator::isDefault($attribute->pattern)) {
                    $metadata->addPropertyConstraint($property->name, new Assert\Regex(pattern: $attribute->pattern, groups: $groups));
                    $loaded = true;
                }

                if (!Generator::isDefault($attribute->uniqueItems)) {
                    $metadata->addPropertyConstraint($property->name, new Assert\Unique(groups: $groups));
                    $loaded = true;
                }

                if (!Generator::isDefault($attribute->enum)) {
                    $enum = $attribute->enum;
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
                        $metadata->addPropertyConstraint($property->name, new Assert\Choice(choices: $choices, groups: $groups));
                        $loaded = true;
                    }
                }

                if (!Generator::isDefault($attribute->multipleOf)) {
                    $metadata->addPropertyConstraint($property->name, new Assert\DivisibleBy(value: $attribute->multipleOf, groups: $groups));
                    $loaded = true;
                }

                if (!Generator::isDefault($attribute->const)) {
                    $metadata->addPropertyConstraint($property->name, new Assert\EqualTo(value: $attribute->const, groups: $groups));
                    $loaded = true;
                }

                if (!$type = $property->getType()) {
                    continue;
                }

                if (!$type->allowsNull()) {
                    $metadata->addPropertyConstraint($property->name, new Assert\NotNull(groups: $groups));
                    $loaded = true;
                }

                if (!$type->isBuiltin()) {
                    $metadata->addPropertyConstraint($property->name, new Assert\Valid(groups: $groups));
                    $loaded = true;
                }
            }
        }

        return $loaded;
    }

    /**
     * @return iterable<Property>
     */
    protected function getAttributes(\ReflectionProperty $reflection): iterable
    {
        foreach ($reflection->getAttributes(Property::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            yield $attribute->newInstance();
        }
    }
}
