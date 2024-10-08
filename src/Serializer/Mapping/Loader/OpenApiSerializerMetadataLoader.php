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

namespace OpenSolid\OpenApiBundle\Serializer\Mapping\Loader;

use OpenSolid\OpenApiBundle\Attribute\Property;
use Symfony\Component\Serializer\Mapping\AttributeMetadata;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Symfony\Component\Serializer\Mapping\Loader\LoaderInterface;

class OpenApiSerializerMetadataLoader implements LoaderInterface
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
