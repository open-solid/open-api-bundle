<?php

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser;

use Exception;
use OpenApi\Analysers\AnalyserInterface;
use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Annotations\OpenApi;
use OpenApi\Context;
use OpenApi\Generator;
use OpenApi\Serializer;

readonly class SerializedAnalyser implements AnalyserInterface
{
    public function __construct(private Serializer $serializer = new Serializer())
    {
    }

    public function setGenerator(Generator $generator): void
    {
        // no-op
    }

    /**
     * @throws Exception
     */
    public function fromFile(string $filename, Context $context): Analysis
    {
        $format = pathinfo($filename, PATHINFO_EXTENSION);

        if (!in_array($format, ['json', 'yaml'], true)) {
            throw new \InvalidArgumentException('Only JSON or YAML files are supported');
        }

        /** @var OpenApi $openapi */
        $openapi = $this->serializer->deserializeFile($filename, $format);

        $this->prepareOpenapiForMerging($openapi, $context);
        $this->prepareComponentsForMerging($openapi, $context);

        return new Analysis([$openapi], $context);
    }

    protected function prepareOpenapiForMerging(OpenApi $openapi, Context $context): void
    {
        $annotations = $openapi->_context?->annotations ?? [];

        foreach ($annotations as $annotation) {
            if ($annotation === $openapi || $annotation instanceof OA\OpenApi) {
                continue;
            }

            if ($annotation instanceof OA\AbstractAnnotation
                && in_array(OA\OpenApi::class, $annotation::$_parents, true)
                && property_exists($annotation, '_context')
                && false === $annotation->_context->is('nested')) {
                $annotation->_context = new Context(['nested' => $openapi], $openapi->_context);
            }
        }
    }

    protected function prepareComponentsForMerging(OpenApi $openapi, Context $context): void
    {
        $annotations = $openapi->_context?->annotations ?? [];

        $components = $openapi->components;
        if (Generator::isDefault($components)) {
            $components = new OA\Components(['_context' => new Context(['generated' => true], $context)]);
        }

        foreach ($annotations as $annotation) {
            if ($annotation instanceof OA\AbstractAnnotation
                && in_array(OA\Components::class, $annotation::$_parents, true)
                && false === $annotation->_context->is('nested')) {
                $annotation->_context = new Context(['nested' => $components], $components->_context);
            }
        }

        $openapi->components = $components;
    }
}
