<?php

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser;

use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Context;

interface AnalyserGuesserInterface
{
    public function guess(\Reflector $reflector, AbstractAnnotation $annotation, Context $context): void;
}
