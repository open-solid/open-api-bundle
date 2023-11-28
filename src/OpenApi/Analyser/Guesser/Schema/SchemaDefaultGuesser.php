<?php

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\Schema;

use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Attributes\Schema;
use OpenApi\Context;
use OpenApi\Generator;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\AnalyserGuesserInterface;

class SchemaDefaultGuesser implements AnalyserGuesserInterface
{
    public function guess(\Reflector $reflector, AbstractAnnotation $annotation, Context $context): void
    {
        if (!$reflector instanceof \ReflectionClass || !$annotation instanceof Schema) {
            return;
        }

        if (Generator::isDefault($annotation->readOnly) && $reflector->isReadOnly()) {
            $annotation->readOnly = true;
        }
    }
}
