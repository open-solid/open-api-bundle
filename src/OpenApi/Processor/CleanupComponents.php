<?php

namespace OpenSolid\OpenApiBundle\OpenApi\Processor;

use OpenApi\Analysis;
use OpenApi\Annotations\Components;
use OpenApi\Generator;
use OpenApi\Processors\ProcessorInterface;

readonly class CleanupComponents implements ProcessorInterface
{
    use ProcessorTrait;

    public function __invoke(Analysis $analysis): void
    {
        if (null === $openapi = $analysis->openapi) {
            return;
        }

        if (Generator::isDefault($openapi->components)) {
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
        if (null === $openapi = $analysis->openapi) {
            return;
        }

        $responses = [];
        foreach ($openapi->components->responses as $i => $response) {
            if (!isset($responses[$response->response])) {
                $responses[$response->response] = $response;

                continue;
            }

            unset($openapi->components->responses[$i]);
            if ($responses[$response->response] !== $response) {
                $this->detachAnnotationRecursively($response, $analysis);
            }
        }

        if (isset($openapi->_unmerged)) {
            foreach ($openapi->_unmerged as $i => $annotation) {
                if ($annotation instanceof Components) {
                    unset($openapi->_unmerged[$i]);
                }
            }
        }
    }

    protected function removeUselessSchemas(Analysis $analysis): void
    {
        if (null === $openapi = $analysis->openapi) {
            return;
        }

        foreach ($openapi->components->schemas as $i => $schema) {
            foreach ($analysis->annotations as $annotation) {
                if (property_exists($annotation, 'ref') && (string) $annotation->ref === '#/components/schemas/'.$schema->schema) {
                    continue 2;
                }
            }

            $this->detachAnnotationRecursively($schema, $analysis);
            unset($openapi->components->schemas[$i]);
        }
    }
}
