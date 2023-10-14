<?php

namespace Yceruto\OpenApiBundle\OpenApi\Analyser;

use OpenApi\Analysers\AnnotationFactoryInterface;
use OpenApi\Analysers\ReflectionAnalyser;

readonly class ReflectionAnalyserFactory
{
    /**
     * @param iterable<AnnotationFactoryInterface> $factories
     */
    public function __invoke(iterable $factories): ReflectionAnalyser
    {
        return new ReflectionAnalyser(iterator_to_array($factories));
    }
}
