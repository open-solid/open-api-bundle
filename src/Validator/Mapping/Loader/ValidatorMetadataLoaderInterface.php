<?php

namespace OpenSolid\OpenApiBundle\Validator\Mapping\Loader;

use ReflectionProperty;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use OpenSolid\OpenApiBundle\Attribute\Property;

interface ValidatorMetadataLoaderInterface
{
    public function load(ClassMetadata $metadata, ReflectionProperty $reflectionProperty, Property $property): bool;
}
