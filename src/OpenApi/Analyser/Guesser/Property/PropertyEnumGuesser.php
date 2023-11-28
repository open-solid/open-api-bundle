<?php

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\Property;

use BackedEnum;
use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Attributes\Property;
use OpenApi\Context;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\AnalyserGuesserInterface;
use ReflectionEnumBackedCase;

class PropertyEnumGuesser implements AnalyserGuesserInterface
{
    public function guess(\Reflector $reflector, AbstractAnnotation $annotation, Context $context): void
    {
        if (!$reflector instanceof \ReflectionProperty || !$annotation instanceof Property) {
            return;
        }

        if (null === $type = $reflector->getType()) {
            return;
        }

        if ($type instanceof \ReflectionNamedType && !$type->isBuiltin() && is_subclass_of($type->getName(), BackedEnum::class)) {
            $enumType = new \ReflectionEnum($type->getName());
            $annotation->enum = array_map(static fn (ReflectionEnumBackedCase $case) => $case->getValue(), $enumType->getCases());
            $annotation->type = $enumType->getBackingType();
        }
    }
}
