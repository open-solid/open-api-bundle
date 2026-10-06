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

namespace OpenSolid\OpenApiBundle\OpenApi\Processor;

use OpenApi\Analysis;
use OpenApi\Undefined;

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

        if (Undefined::isDefault($openapi->components) || Undefined::isDefault($openapi->components->responses)) {
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

        if (Undefined::isDefault($openapi->components) || Undefined::isDefault($openapi->components->responses)) {
            return;
        }

        foreach ($openapi->components->responses as $i => $response) {
            if (!Undefined::isDefault($openapi->paths)) {
                foreach ($openapi->paths as $pathItem) {
                    foreach ($this->operationsOf($pathItem) as $method) {
                        if (Undefined::isDefault($method->responses)) {
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
            $openapi->components->responses = Undefined::UNDEFINED;
        }
    }

    protected function removeComponentsUselessParameters(Analysis $analysis): void
    {
        if (null === $openapi = $analysis->openapi) {
            return;
        }

        if (Undefined::isDefault($openapi->components) || Undefined::isDefault($openapi->components->parameters)) {
            return;
        }

        foreach ($openapi->components->parameters as $i => $parameter) {
            if (!Undefined::isDefault($parameter->name) && !Undefined::isDefault($parameter->parameter)) {
                continue;
            }

            $this->detachAnnotationRecursively($parameter, $analysis);
            unset($openapi->components->parameters[$i]);
        }

        if ([] === $openapi->components->parameters) {
            $openapi->components->parameters = Undefined::UNDEFINED;
        }
    }

    protected function removeComponentsUselessSchemas(Analysis $analysis): void
    {
        if (null === $openapi = $analysis->openapi) {
            return;
        }

        if (Undefined::isDefault($openapi->components) || Undefined::isDefault($openapi->components->schemas)) {
            return;
        }

        $schemas = [];
        foreach ($openapi->components->schemas as $schema) {
            $schemas['#/components/schemas/'.$schema->schema] = $schema;
        }

        // refs used outside the schemas, then the refs those schemas use in turn
        $components = $openapi->components;
        $openapi->components = clone $components;
        $openapi->components->schemas = Undefined::UNDEFINED;
        $refs = $this->collectRefs($openapi);
        $openapi->components = $components;

        $pending = array_keys($refs);
        while (null !== $ref = array_pop($pending)) {
            if (!isset($schemas[$ref])) {
                continue;
            }

            foreach ($this->collectRefs($schemas[$ref]) as $nestedRef => $_) {
                if (!isset($refs[$nestedRef])) {
                    $refs[$nestedRef] = true;
                    $pending[] = $nestedRef;
                }
            }
        }

        foreach ($openapi->components->schemas as $i => $schema) {
            if (isset($refs['#/components/schemas/'.$schema->schema])) {
                continue;
            }

            $this->detachAnnotationRecursively($schema, $analysis);
            unset($openapi->components->schemas[$i]);
        }

        if ([] === $openapi->components->schemas) {
            $openapi->components->schemas = Undefined::UNDEFINED;
        }
    }
}
