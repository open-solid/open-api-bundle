<?php

namespace Yceruto\OpenApiBundle\Mapping\Validator\Loader;

use ReflectionProperty;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Yceruto\OpenApiBundle\Attributes\Property;

readonly class FormatMetadataLoader implements ValidatorMetadataLoaderInterface
{
    public function __construct(private int $passwordMinScore = Assert\PasswordStrength::STRENGTH_VERY_STRONG)
    {
    }

    public function load(ClassMetadata $metadata, ReflectionProperty $reflectionProperty, Property $property): bool
    {
        $groups = $property->groups;
        $loaded = false;

        if ('uuid' === $property->format) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\Uuid(groups: $groups));
            $loaded = true;
        }

        if ('email' === $property->format) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\Email(groups: $groups));
            $loaded = true;
        }

        if ('password' === $property->format) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\PasswordStrength(minScore: $this->passwordMinScore, groups: $groups));
            $loaded = true;
        }

        if ('date' === $property->format) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\Date(groups: $groups));
            $loaded = true;
        }

        if ('date-time' === $property->format) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\DateTime(groups: $groups));
            $loaded = true;
        }

        if ('currency' === $property->format) {
            $metadata->addPropertyConstraint($reflectionProperty->name, new Assert\Currency(groups: $groups));
            $loaded = true;
        }

        return $loaded;
    }
}
