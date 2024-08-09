<?php

declare(strict_types=1);

/*
 * This file is part of OpenSolid package.
 *
 * (c) Yonel Ceruto <open@yceruto.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
