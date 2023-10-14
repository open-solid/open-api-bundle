<?php

namespace Yceruto\OpenApiBundle\OpenApi\Processor;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\Processors\ProcessorInterface;

readonly class MergeConfigIntoOpenApi implements ProcessorInterface
{
    public function __construct(private array $spec)
    {
    }

    public function __invoke(Analysis $analysis): void
    {
        if (null === $openApi = $analysis->openapi) {
            return;
        }

        if (isset($this->spec['openapi'])) {
            $openApi->openapi = $this->spec['openapi'];
        }

        if (isset($this->spec['info']) && Generator::isDefault($openApi->info)) {
            $openApi->info = new OA\Info($this->spec['info']);
        }

        if (isset($this->spec['servers']) && Generator::isDefault($openApi->servers)) {
            $openApi->servers = array_map(
                static fn (array $properties) => new OA\Server($properties),
                $this->spec['servers'],
            );
        }
    }
}
