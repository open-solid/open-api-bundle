<?php

namespace OpenSolid\OpenApiBundle\OpenApi\Processor;

use OpenApi\Analysis;
use OpenApi\Generator;
use OpenApi\Processors\ProcessorInterface;

readonly class CleanupAnnotations implements ProcessorInterface
{
    use ProcessorTrait;

    public function __invoke(Analysis $analysis): void
    {
        $this->removeComponentsDuplicatedResponses($analysis);
        $this->removeComponentsUselessParameters($analysis);
        $this->removeComponentsUselessSchemas($analysis);
    }

    protected function removeComponentsDuplicatedResponses(Analysis $analysis): void
    {
        if (null === $openapi = $analysis->openapi) {
            return;
        }

        if (Generator::isDefault($openapi->components)) {
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
    }

    protected function removeComponentsUselessParameters(Analysis $analysis): void
    {
        if (null === $openapi = $analysis->openapi) {
            return;
        }

        if (Generator::isDefault($openapi->components) || Generator::isDefault($openapi->components->parameters)) {
            return;
        }

        foreach ($openapi->components->parameters as $i => $parameter) {
            if (!Generator::isDefault($parameter->name) && !Generator::isDefault($parameter->parameter)) {
                continue;
            }

            $this->detachAnnotationRecursively($parameter, $analysis);
            unset($openapi->components->parameters[$i]);
        }

        if ([] === $openapi->components->parameters) {
            $openapi->components->parameters = Generator::UNDEFINED;
        }
    }

    protected function removeComponentsUselessSchemas(Analysis $analysis): void
    {
        if (null === $openapi = $analysis->openapi) {
            return;
        }

        if (Generator::isDefault($openapi->components)) {
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
