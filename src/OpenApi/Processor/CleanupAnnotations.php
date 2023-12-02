<?php

namespace OpenSolid\OpenApiBundle\OpenApi\Processor;

use OpenApi\Analysis;
use OpenApi\Annotations\Operation;
use OpenApi\Generator;
use OpenApi\Processors\ProcessorInterface;
use OpenSolid\OpenApiBundle\Routing\Attribute\Delete;
use OpenSolid\OpenApiBundle\Routing\Attribute\Get;
use OpenSolid\OpenApiBundle\Routing\Attribute\Patch;
use OpenSolid\OpenApiBundle\Routing\Attribute\Post;
use OpenSolid\OpenApiBundle\Routing\Attribute\Put;

readonly class CleanupAnnotations implements ProcessorInterface
{
    use ProcessorTrait;

    public function __invoke(Analysis $analysis): void
    {
        $this->removeComponentsDuplicatedResponses($analysis);
        $this->removeComponentsUselessResponses($analysis);
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

    protected function removeComponentsUselessResponses(Analysis $analysis): void
    {
        if (null === $openapi = $analysis->openapi) {
            return;
        }

        if (Generator::isDefault($openapi->components)) {
            return;
        }

        foreach ($openapi->components->responses as $i => $response) {
            if (!Generator::isDefault($openapi->paths)) {
                foreach ($openapi->paths as $pathItem) {
                    /** @var Operation[]|Post[]|Get[]|Put[]|Patch[]|Delete[] $methods */
                    $methods = [
                        $pathItem->post,
                        $pathItem->get,
                        $pathItem->put,
                        $pathItem->patch,
                        $pathItem->delete,
                    ];
                    foreach ($methods as $method) {
                        if (Generator::isDefault($method) || Generator::isDefault($method->responses)) {
                            continue;
                        }

                        foreach ($method->responses as $res) {
                            if ((int) $res->response === (int) $response->response) {
                                continue 4;
                            }
                        }
                    }
                }
            }

            $this->detachAnnotationRecursively($response, $analysis);
            unset($openapi->components->responses[$i]);
        }

        if ([] === $openapi->components->responses) {
            $openapi->components->responses = Generator::UNDEFINED;
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

        if ([] === $openapi->components->schemas) {
            $openapi->components->schemas = Generator::UNDEFINED;
        }
    }
}
