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
            throw new \InvalidArgumentException('Only JSON or YAML files are supported.');
        }

        /** @var OpenApi $openapi */
        $openapi = $this->serializer->deserializeFile($filename, $format);

        if (!Generator::isDefault($openapi->paths)) {
            throw new \InvalidArgumentException('Only OpenAPI files with no paths are supported.');
        }

        $annotations = [];
        foreach ($openapi->_context?->annotations ?? [] as $annotation) {
            if ($annotation instanceof OA\AbstractAnnotation
                && in_array(OA\OpenApi::class, $annotation::$_parents, true)) {
                // A top level annotation.
                $annotations[] = $annotation;
            }
        }

        if ($openapi->openapi !== OpenApi::DEFAULT_VERSION) {
            $annotations[] = new OpenApi([
                'openapi' => $openapi->openapi,
                '_context' => $openapi->_context,
            ]);
        }

        return new Analysis($annotations, $context);
    }
}
