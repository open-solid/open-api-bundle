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

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser;

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
     * @throws \Exception
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
            if ($annotation instanceof OA\Components) {
                continue;
            }
            if ($annotation instanceof OA\AbstractAnnotation
                && (in_array(OpenApi::class, $annotation::$_parents, true)
                    || in_array(OA\Components::class, $annotation::$_parents, true))) {
                $annotations[] = $annotation;
            }
        }

        if (OpenApi::DEFAULT_VERSION !== $openapi->openapi) {
            $annotations[] = new OpenApi([
                'openapi' => $openapi->openapi,
                '_context' => $openapi->_context,
            ]);
        }

        return new Analysis($annotations, $context);
    }
}
