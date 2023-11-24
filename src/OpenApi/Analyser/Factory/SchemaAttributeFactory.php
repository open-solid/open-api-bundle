<?php

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Factory;

use OpenApi\Attributes\Schema;
use OpenApi\Context;
use OpenApi\Generator;

class SchemaAttributeFactory implements AttributeFactoryInterface
{
    public function build(\Reflector $reflector, array $annotations, Context $context): iterable
    {
        if (!$reflector instanceof \ReflectionClass) {
            return [];
        }

        foreach ($annotations as $annotation) {
            if (!$annotation instanceof Schema) {
                continue;
            }

            if (Generator::isDefault($annotation->readOnly) && $reflector->isReadOnly()) {
                $annotation->readOnly = true;
            }
        }

        return [];
    }
}
