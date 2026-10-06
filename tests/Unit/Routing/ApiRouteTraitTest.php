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

namespace OpenSolid\Tests\OpenApiBundle\Unit\Routing;

use OpenSolid\OpenApiBundle\Routing\Attribute\Delete;
use OpenSolid\OpenApiBundle\Routing\Attribute\Get;
use OpenSolid\OpenApiBundle\Routing\Attribute\Head;
use OpenSolid\OpenApiBundle\Routing\Attribute\Options;
use OpenSolid\OpenApiBundle\Routing\Attribute\Patch;
use OpenSolid\OpenApiBundle\Routing\Attribute\Post;
use OpenSolid\OpenApiBundle\Routing\Attribute\Put;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;

class ApiRouteTraitTest extends TestCase
{
    public static function provideAttributes(): iterable
    {
        yield [Get::class, 'GET'];
        yield [Post::class, 'POST'];
        yield [Put::class, 'PUT'];
        yield [Patch::class, 'PATCH'];
        yield [Delete::class, 'DELETE'];
        yield [Head::class, 'HEAD'];
        yield [Options::class, 'OPTIONS'];
    }

    #[DataProvider('provideAttributes')]
    public function testRoute(string $class, string $method): void
    {
        $attribute = new $class('/foo', name: 'foo', requirements: ['id' => '\d+'], statusCode: 299);

        $this->assertSame($method, $attribute->getMethod());
        $this->assertSame([$method], $attribute->route->methods);
        $this->assertSame('/foo', $attribute->route->path);
        $this->assertSame('foo', $attribute->operationId);
        $this->assertSame(['id' => '\d+'], $attribute->requirements);
        $this->assertSame(299, $attribute->statusCode);
    }

    public function testConditions(): void
    {
        $this->assertNull((new Get('/foo'))->route->condition);
        $this->assertSame('a', (new Get('/foo', when: 'a'))->route->condition);
        $this->assertSame('a and b', (new Get('/foo', condition: 'a', when: 'b'))->route->condition);
    }

    public function testUnknownPropertyIsNull(): void
    {
        $attribute = new Get('/foo');
        $logger = new class extends AbstractLogger {
            public array $messages = [];

            public function log($level, $message, array $context = []): void
            {
                $this->messages[] = (string) $message;
            }
        };
        $attribute->_context->logger = $logger;

        $this->assertNull($attribute->unknown);
        $this->assertSame(['Property "unknown" doesn\'t exist in a @'.Get::class.'()'], $logger->messages);
    }

    public function testCustomPropertiesAreNotSerialized(): void
    {
        $data = json_decode(json_encode(new Get('/foo', name: 'foo', itemsType: 'Foo', when: 'true', statusCode: 299)), true);

        $this->assertSame(['operationId' => 'foo'], $data);
    }
}
