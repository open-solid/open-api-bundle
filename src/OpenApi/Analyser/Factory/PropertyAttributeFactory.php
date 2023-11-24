<?php

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Factory;

use IntBackedEnum;
use OpenApi\Attributes\Property;
use OpenApi\Context;
use OpenApi\Generator;
use UnitEnum;

class PropertyAttributeFactory implements AttributeFactoryInterface
{
    public function build(\Reflector $reflector, array $annotations, Context $context): iterable
    {
        if (!$reflector instanceof \ReflectionProperty || !$type = $reflector->getType()) {
            return [];
        }

        foreach ($annotations as $annotation) {
            if (!$annotation instanceof Property) {
                continue;
            }

            if (Generator::isDefault($annotation->default) && $reflector->hasDefaultValue() && null !== $default = $reflector->getDefaultValue()) {
                $annotation->default = $default;
            }

            if ($type instanceof \ReflectionNamedType && !$type->isBuiltin() && is_subclass_of($type->getName(), UnitEnum::class)) {
                $annotation->enum = $type->getName()::cases();
                $annotation->type = is_subclass_of($type->getName(), IntBackedEnum::class) ? 'integer' : 'string';
            }

            if ($reflector->isReadOnly() && Generator::isDefault($annotation->readOnly)) {
                $annotation->readOnly = true;
            }
        }

        return [];
    }
}
