<?php

namespace OpenSolid\OpenApiBundle\Validator\Mapping\Loader;

use OpenSolid\OpenApiBundle\Attribute\Property;
use ReflectionProperty;
use Symfony\Component\Validator\Mapping\ClassMetadata;

interface ValidatorMetadataLoaderInterface
{
    public function load(ClassMetadata $metadata, ReflectionProperty $reflectionProperty, Property $property): bool;
}
