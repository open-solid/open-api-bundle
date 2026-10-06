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

namespace OpenSolid\Tests\OpenApiBundle\Unit\Controller;

use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\ReflectionAnalyser;
use OpenSolid\OpenApiBundle\Controller\OpenApiController;
use OpenSolid\OpenApiBundle\Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OpenApiControllerTest extends TestCase
{
    public static function provideActions(): iterable
    {
        yield ['index'];
        yield ['yaml'];
        yield ['json'];
        yield ['jsonSchema'];
    }

    #[DataProvider('provideActions')]
    public function testSpecNotFound(string $action): void
    {
        $controller = new OpenApiController($this->createGenerator(__DIR__.'/Fixtures/Empty'));

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('OpenAPI spec not found.');

        match ($action) {
            'index' => $controller->index($this->createStub(UrlGeneratorInterface::class)),
            'jsonSchema' => $controller->jsonSchema(new Request(), 'Foo'),
            default => $controller->{$action}(),
        };
    }

    public function testSchemaNotFound(): void
    {
        $controller = new OpenApiController($this->createGenerator(__DIR__.'/Fixtures/Invalid'));

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Schema "Foo" not found.');

        $controller->jsonSchema(new Request(), 'Foo');
    }

    public function testValidationErrorsAreShown(): void
    {
        $controller = new OpenApiController($this->createGenerator(__DIR__.'/Fixtures/Invalid'));
        $content = $controller->index($this->createStub(UrlGeneratorInterface::class))->getContent();

        $this->assertStringContainsString('Required @OA\Info() not found', $content);
    }

    public function testValidationWithoutMessages(): void
    {
        $controller = new OpenApiController($this->createGenerator(__DIR__.'/Fixtures/Invalid', new NullLogger()));
        $content = $controller->index($this->createStub(UrlGeneratorInterface::class))->getContent();

        $this->assertStringContainsString('OpenAPI spec is invalid.', $content);
    }

    private function createGenerator(string $path, ?NullLogger $logger = null): Generator
    {
        return new Generator(new ReflectionAnalyser([new AttributeAnnotationFactory()]), [], [$path], $logger);
    }
}
