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

use OpenSolid\OpenApiBundle\Attribute\Property;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\LoaderInterface;

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
