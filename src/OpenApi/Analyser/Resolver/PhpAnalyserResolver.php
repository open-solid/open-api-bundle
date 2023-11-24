<?php

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Resolver;

use OpenApi\Analysers\AnalyserInterface;
use OpenApi\Analysers\ReflectionAnalyser;

readonly class PhpAnalyserResolver implements AnalyserResolverInterface
{
    public function __construct(private ReflectionAnalyser $reflectionAnalyser)
    {
    }

    public function resolve(string $filename, string $format): ?AnalyserInterface
    {
        return 'php' === $format ? $this->reflectionAnalyser : null;
    }
}
