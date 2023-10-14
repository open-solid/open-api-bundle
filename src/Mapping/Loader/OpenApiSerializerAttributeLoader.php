<?php

namespace Yceruto\OpenApiBundle\Mapping\Loader;

use Symfony\Component\Serializer\Mapping\AttributeMetadata;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Symfony\Component\Serializer\Mapping\Loader\LoaderInterface;
use Yceruto\OpenApiBundle\Attributes\Property;

class OpenApiSerializerAttributeLoader implements LoaderInterface
{
    public function loadClassMetadata(ClassMetadataInterface $classMetadata): bool
    {
        $attributesMetadata = $classMetadata->getAttributesMetadata();
        $reflectionClass = $classMetadata->getReflectionClass();
        $loaded = false;

        foreach ($reflectionClass->getProperties() as $property) {
            if (!isset($attributesMetadata[$property->name])) {
                $attributesMetadata[$property->name] = new AttributeMetadata($property->name);
                $classMetadata->addAttributeMetadata($attributesMetadata[$property->name]);
            }

            foreach ($this->getAttributes($property) as $attribute) {
                if (null !== $attribute->groups) {
                    array_map($attributesMetadata[$property->name]->addGroup(...), $attribute->groups);
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
