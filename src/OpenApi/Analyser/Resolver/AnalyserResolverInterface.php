<?php

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Resolver;

use OpenApi\Analysers\AnalyserInterface;

interface AnalyserResolverInterface
{
    public function resolve(string $filename, string $format): ?AnalyserInterface;
}
