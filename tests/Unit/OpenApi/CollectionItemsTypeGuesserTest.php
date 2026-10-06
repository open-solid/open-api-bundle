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

use OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\CollectionItemsTypeGuesser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CollectionItemsTypeGuesserTest extends TestCase
{
    public static function provideReturnTypes(): iterable
    {
        yield ['objects', \stdClass::class];
        yield ['nullableObjects', \stdClass::class];
        yield ['integers', 'integer'];
        yield ['floats', 'number'];
        yield ['booleans', 'boolean'];
        yield ['strings', 'string'];
        yield ['mixed', null];
        yield ['unions', null];
        yield ['notACollection', null];
        yield ['invalidDocblock', null];
    }

    #[DataProvider('provideReturnTypes')]
    public function testGuessReturnType(string $method, ?string $expected): void
    {
        $this->assertSame($expected, CollectionItemsTypeGuesser::guess(new \ReflectionMethod(CollectionFixture::class, $method)));
    }

    public function testGuessParameterType(): void
    {
        $this->assertSame(\stdClass::class, CollectionItemsTypeGuesser::guess(new \ReflectionParameter([CollectionFixture::class, 'parameter'], 'items')));
    }
}

class CollectionFixture
{
    /** @return list<\stdClass> */
    public function objects(): array
    {
        return [];
    }

    /** @return list<\stdClass>|null */
    public function nullableObjects(): ?array
    {
        return null;
    }

    /** @return list<int> */
    public function integers(): array
    {
        return [];
    }

    /** @return list<float> */
    public function floats(): array
    {
        return [];
    }

    /** @return list<bool> */
    public function booleans(): array
    {
        return [];
    }

    /** @return array<string, string> */
    public function strings(): array
    {
        return [];
    }

    public function mixed(): array
    {
        return [];
    }

    /** @return list<int|string> */
    public function unions(): array
    {
        return [];
    }

    public function notACollection(): string
    {
        return '';
    }

    /** @return list<UnknownClass> */
    public function invalidDocblock(): array
    {
        return [];
    }

    /** @param \stdClass[] $items */
    public function parameter(array $items): void
    {
    }
}
