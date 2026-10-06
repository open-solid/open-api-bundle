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
use OpenApi\Utils\Pipeline;
use Psr\Log\LoggerInterface;

readonly class Generator
{
    /**
     * @param iterable<callable> $processors
     * @param string[]           $paths
     */
    public function __construct(
        private AnalyserInterface $analyser,
        private iterable $processors,
        private array $paths,
        private ?LoggerInterface $logger = null,
    ) {
    }

    public function generate(): ?OA\OpenApi
    {
        return (new OpenApiGenerator($this->logger))
            ->setAnalyser($this->analyser)
            ->setProcessorPipeline(new Pipeline(iterator_to_array($this->processors, false)))
            ->generate($this->paths, validate: false);
    }
}
