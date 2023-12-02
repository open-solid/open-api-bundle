<?php

namespace OpenSolid\OpenApiBundle;

use OpenApi\Analysers\AnalyserInterface;
use OpenApi\Annotations as OA;
use OpenApi\Generator as OpenApiGenerator;
use OpenApi\Processors;

readonly class Generator
{
    /**
     * @param array<Processors\ProcessorInterface> $processors
     * @param string[]                             $paths
     */
    public function __construct(
        private AnalyserInterface $analyser,
        private iterable $processors,
        private array $paths,
    ) {
    }

    public function generate(): ?OA\OpenApi
    {
        return OpenApiGenerator::scan($this->paths, [
            'analyser' => $this->analyser,
            'processors' => iterator_to_array($this->processors),
            'validate' => false,
        ]);
    }
}
