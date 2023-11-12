<?php

namespace Yceruto\OpenApiBundle\OpenApi\Processor;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\Processors\ProcessorInterface;

class AugmentSchemas implements ProcessorInterface
{
    public function __invoke(Analysis $analysis): void
    {
        /** @var OA\Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(OA\Schema::class);

        $this->augmentRequired($schemas);
    }

    /**
     * @param OA\Schema[] $schemas
     */
    protected function augmentRequired(array $schemas): void
    {
        foreach ($schemas as $schema) {
            if ('object' !== $schema->type || Generator::isDefault($schema->properties)) {
                continue;
            }

            if (true !== $schema->writeOnly || !Generator::isDefault($schema->required)) {
                continue;
            }

            $required = [];
            foreach ($schema->properties as $property) {
                if (true !== $property->nullable) {
                    $required[] = $property->property;
                }
            }

            if ([] === $required) {
                continue;
            }

            $schema->required = $required;
        }
    }
}
