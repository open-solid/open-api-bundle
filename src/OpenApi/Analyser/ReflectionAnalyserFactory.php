<?php

namespace Yceruto\OpenApiBundle\OpenApi\Analyser;

use OpenApi\Analysers\AnnotationFactoryInterface;
use OpenApi\Analysers\ReflectionAnalyser;
use Traversable;

readonly class ReflectionAnalyserFactory
{
    /**
     * @param Traversable<AnnotationFactoryInterface> $factories
     */
    public function __invoke(Traversable $factories): ReflectionAnalyser
    {
        return new ReflectionAnalyser(iterator_to_array($factories));
    }
}
