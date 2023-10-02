<?php

namespace Yceruto\OpenApiBundle;

use OpenApi\Analysers;
use OpenApi\Annotations as OA;
use OpenApi\Generator as OpenApiGenerator;
use OpenApi\Processors;

readonly class Generator
{
    /**
     * @param array<Analysers\AnnotationFactoryInterface> $factories
     * @param array<Processors\ProcessorInterface>        $processors
     * @param string[]                                    $scanDirs
     */
    public function __construct(
        private iterable $factories,
        private iterable $processors,
        private array $scanDirs,
        private array $spec,
    ) {
    }

    public function generate(): OA\OpenApi
    {
        $openapi = OpenApiGenerator::scan($this->scanDirs, [
            'analyser' => new Analysers\ReflectionAnalyser(array_merge([
                new Analysers\DocBlockAnnotationFactory(),
            ], iterator_to_array($this->factories))),
            'processors' => array_merge([
                new Processors\DocBlockDescriptions(),
                new Processors\MergeIntoOpenApi(),
                new Processors\MergeIntoComponents(),
                new Processors\ExpandClasses(),
                new Processors\ExpandInterfaces(),
                new Processors\ExpandTraits(),
                new Processors\ExpandEnums(),
                new Processors\AugmentSchemas(),
                new Processors\AugmentProperties(),
                new Processors\BuildPaths(),
                new Processors\AugmentParameters(),
                new Processors\AugmentRefs(),
                new Processors\MergeJsonContent(),
                new Processors\MergeXmlContent(),
                new Processors\OperationId(),
                new Processors\CleanUnmerged(),
            ], iterator_to_array($this->processors)),
        ]) ?? new OA\OpenApi([]);

        if (isset($this->spec['openapi']) && OpenApiGenerator::isDefault($openapi->openapi)) {
            $openapi->openapi = $this->spec['openapi'];
        }

        if (isset($this->spec['info']) && OpenApiGenerator::isDefault($openapi->info)) {
            $openapi->info = new OA\Info($this->spec['info']);
        }

        if (isset($this->spec['servers']) && OpenApiGenerator::isDefault($openapi->servers)) {
            $openapi->servers = array_map(
                static fn (array $properties) => new OA\Server($properties),
                $this->spec['servers'],
            );
        }

        return $openapi;
    }
}
