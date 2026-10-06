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

namespace OpenSolid\Tests\OpenApiBundle\Unit\OpenApi;

use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\ReflectionAnalyser;
use OpenApi\Context;
use OpenApi\Generator;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\ResolvableAnalyser;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Resolver\PhpAnalyserResolver;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Resolver\SerializedAnalyserResolver;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\SerializedAnalyser;
use PHPUnit\Framework\TestCase;

class AnalyserTest extends TestCase
{
    public function testUnsupportedFileHasNoAnalyser(): void
    {
        $analyser = new ResolvableAnalyser([
            new PhpAnalyserResolver(new ReflectionAnalyser([new AttributeAnnotationFactory()])),
            new SerializedAnalyserResolver(new SerializedAnalyser()),
        ]);
        $analyser->setGenerator(new Generator());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No analyser found for file "'.__DIR__.'/Fixtures/spec.txt".');

        $analyser->fromFile(__DIR__.'/Fixtures/spec.txt', new Context());
    }

    public function testSerializedAnalyserOnlySupportsJsonAndYaml(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Only JSON or YAML files are supported.');

        (new SerializedAnalyser())->fromFile(__DIR__.'/Fixtures/spec.txt', new Context());
    }

    public function testSerializedAnalyserRejectsPaths(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Only OpenAPI files with no paths are supported.');

        (new SerializedAnalyser())->fromFile(__DIR__.'/Fixtures/with_paths.yaml', new Context());
    }

    public function testSerializedAnalyserIgnoresTheGenerator(): void
    {
        $analyser = new SerializedAnalyser();

        $this->assertSame($analyser, $analyser->setGenerator(new Generator()));
    }
}
