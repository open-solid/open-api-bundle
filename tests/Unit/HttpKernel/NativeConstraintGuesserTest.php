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

namespace OpenSolid\Tests\OpenApiBundle\Unit\HttpKernel;

use OpenSolid\OpenApiBundle\Attribute\Path;
use OpenSolid\OpenApiBundle\HttpKernel\Controller\ValueResolver\ConstraintGuesser\NativeConstraintGuesser;
use OpenSolid\Tests\OpenApiBundle\Unit\HttpKernel\Fixtures\DummyStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class NativeConstraintGuesserTest extends TestCase
{
    public static function provideFormats(): iterable
    {
        yield ['uuid', Assert\Uuid::class];
        yield ['date', Assert\Date::class];
        yield ['date-time', Assert\DateTime::class];
        yield ['datetime', Assert\DateTime::class];
        yield ['locale', Assert\Locale::class];
        yield ['currency', Assert\Currency::class];
        yield ['numeric', Assert\Type::class];
    }

    #[DataProvider('provideFormats')]
    public function testFormat(string $format, string $constraint): void
    {
        $constraints = (new NativeConstraintGuesser())->guess($this->argument(), new Path(format: $format));

        $this->assertCount(1, $constraints);
        $this->assertInstanceOf($constraint, array_values($constraints)[0]);
    }

    public function testUnknownFormat(): void
    {
        $this->assertSame([], (new NativeConstraintGuesser())->guess($this->argument(), new Path(format: 'unknown')));
    }

    public function testEnumClass(): void
    {
        $constraints = array_values((new NativeConstraintGuesser())->guess($this->argument(), new Path(enum: DummyStatus::class)));

        $this->assertInstanceOf(Assert\Choice::class, $constraints[0]);
        $this->assertSame(['draft'], $constraints[0]->choices);
        $this->assertSame('This is not a valid id value.', $constraints[0]->message);
    }

    public function testEnumValues(): void
    {
        $constraints = array_values((new NativeConstraintGuesser())->guess($this->argument(), new Path(enum: [DummyStatus::Draft, Pure::Foo, 'bar'])));

        $this->assertSame(['draft', 'Foo', 'bar'], $constraints[0]->choices);
    }

    private function argument(): ArgumentMetadata
    {
        return new ArgumentMetadata('id', 'string', false, false, null);
    }
}

enum Pure
{
    case Foo;
}
