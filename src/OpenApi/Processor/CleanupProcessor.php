<?php

namespace Yceruto\OpenApiBundle\OpenApi\Processor;

use OpenApi\Analysis;
use OpenApi\Generator;
use OpenApi\Processors\ProcessorInterface;

readonly class CleanupProcessor implements ProcessorInterface
{
    use ProcessorTrait;

    public function __invoke(Analysis $analysis): void
    {
        if (Generator::isDefault($analysis->openapi->components)) {
            return;
        }

        foreach ($analysis->openapi->components->schemas as $i => $schema) {
            foreach ($analysis->annotations as $annotation) {
                if (property_exists($annotation, 'ref') && (string) $annotation->ref === '#/components/schemas/'.$schema->schema) {
                    continue 2;
                }
            }

            $this->detachAnnotationRecursively($schema, $analysis);
            unset($analysis->openapi->components->schemas[$i]);
        }
    }

    public static function priority(): int
    {
        return -100;
    }
}
