<?php

namespace Yceruto\OpenApiBundle\Mapping\Validator\Loader;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\LoaderInterface;
use Yceruto\OpenApiBundle\Attribute\Property;

readonly class OpenApiValidatorMetadataLoader implements LoaderInterface
{
    /**
     * @param iterable<ValidatorMetadataLoaderInterface> $loaders
     */
    public function __construct(private iterable $loaders)
    {
    }

    public function loadClassMetadata(ClassMetadata $metadata): bool
    {
        $reflector = $metadata->getReflectionClass();
        $loaded = false;

        foreach ($reflector->getProperties() as $reflectionProperty) {
            foreach ($this->getAttributes($reflectionProperty) as $property) {
                foreach ($this->loaders as $loader) {
                    if ($loader->load($metadata, $reflectionProperty, $property)) {
                        $loaded = true;
                    }
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
