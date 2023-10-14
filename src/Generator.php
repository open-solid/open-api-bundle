<?php

namespace Yceruto\OpenApiBundle;

use OpenApi\Analysers\AnalyserInterface;
use OpenApi\Annotations as OA;
use OpenApi\Generator as OpenApiGenerator;
use OpenApi\Processors;

readonly class Generator
{
    /**
     * @param array<Processors\ProcessorInterface> $processors
     * @param string[]                             $scanDirs
     */
    public function __construct(
        private AnalyserInterface $analyser,
        private iterable $processors,
        private array $scanDirs,
    ) {
    }

    public function generate(): OA\OpenApi
    {
        return OpenApiGenerator::scan($this->scanDirs, [
            'analyser' => $this->analyser,
            'processors' => iterator_to_array($this->processors),
            'validate' => true,
        ]) ?? new OA\OpenApi([]);
    }
}
