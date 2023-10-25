<?php

namespace Yceruto\OpenApiBundle\OpenApi\Processor;

use OpenApi\Analysis;
use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Generator;
use OpenApi\Processors\ProcessorInterface;

readonly class CleanupProcessor implements ProcessorInterface
{
    public function __invoke(Analysis $analysis): void
    {
        if (Generator::isDefault($analysis->openapi->components)) {
            return;
        }

        $this->removeDuplicatedResponses($analysis);
        $this->removeUselessSchemas($analysis);
    }

    public static function priority(): int
    {
        return -100;
    }

    protected function removeDuplicatedResponses(Analysis $analysis): void
    {
        foreach ($analysis->openapi->components->responses as $i => $response) {
            if (!isset($responses[$response->response])) {
                $responses[$response->response] = true;

                continue;
            }

            unset($analysis->openapi->components->responses[$i]);
            $this->detachAnnotationRecursively($response, $analysis);
        }
    }

    protected function removeUselessSchemas(Analysis $analysis): void
    {
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

    protected function detachAnnotationRecursively($annotation, Analysis $analysis): void
    {
        if ($annotation instanceof AbstractAnnotation) {
            $analysis->annotations->detach($annotation);
        }

        if (is_array($annotation) || is_object($annotation)) {
            foreach ($annotation as $field) {
                if (is_array($field) || $field instanceof AbstractAnnotation) {
                    $this->detachAnnotationRecursively($field, $analysis);
                }
            }
        }
    }
}
