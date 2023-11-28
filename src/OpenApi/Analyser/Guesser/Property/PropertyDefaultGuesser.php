<?php

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\Property;

use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Attributes\Property;
use OpenApi\Context;
use OpenApi\Generator;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\AnalyserGuesserInterface;

class PropertyDefaultGuesser implements AnalyserGuesserInterface
{
    public function guess(\Reflector $reflector, AbstractAnnotation $annotation, Context $context): void
    {
        if (!$reflector instanceof \ReflectionProperty || !$annotation instanceof Property) {
            return;
        }

        if (Generator::isDefault($annotation->default) && $reflector->hasDefaultValue() && null !== $default = $reflector->getDefaultValue()) {
            $annotation->default = $default;
        }

        if (Generator::isDefault($annotation->readOnly) && $reflector->isReadOnly()) {
            $annotation->readOnly = true;
        }
    }
}
