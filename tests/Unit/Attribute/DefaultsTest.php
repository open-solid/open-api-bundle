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

namespace OpenSolid\Tests\OpenApiBundle\Unit\Attribute;

use OpenApi\Attributes\Schema;
use OpenApi\Undefined;
use OpenSolid\OpenApiBundle\Attribute\Path;
use OpenSolid\OpenApiBundle\Attribute\PathDefaults;
use OpenSolid\OpenApiBundle\Attribute\Property;
use OpenSolid\OpenApiBundle\Attribute\PropertyDefaults;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DefaultsTest extends TestCase
{
    public static function provideDefaults(): iterable
    {
        yield [PathDefaults::class];
        yield [PropertyDefaults::class];
    }

    /**
     * @param class-string<PathDefaults|PropertyDefaults> $class
     */
    #[DataProvider('provideDefaults')]
    public function testSetters(string $class): void
    {
        $defaults = $class::create();

        foreach ((new \ReflectionClass($class))->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isStatic()) {
                continue;
            }

            $value = $this->sampleOf($method->getParameters()[0]);
            $this->assertSame($defaults, $method->invoke($defaults, $value), $method->name);
            $this->assertSame($value, $defaults->{$method->name}, $method->name);
        }
    }

    public function testBooleanSettersDefaultToTrue(): void
    {
        $this->assertTrue(PathDefaults::create()->required()->required);
        $this->assertTrue(PropertyDefaults::create()->exclusiveMinimum()->exclusiveMinimum);
    }

    public function testPathUsesItsDefaults(): void
    {
        $path = new IdPath();

        $this->assertSame('id', $path->name);
        $this->assertSame('The identifier', $path->description);
        $this->assertTrue($path->required);
        $this->assertSame('uuid', $path->format);
        $this->assertSame(['a'], $path->enum);

        $path = new IdPath(name: 'other', required: false, format: 'ulid', enum: ['b']);

        $this->assertSame('other', $path->name);
        $this->assertFalse($path->required);
        $this->assertSame('ulid', $path->format);
        $this->assertSame(['b'], $path->enum);
    }

    public function testPathWithoutDefaults(): void
    {
        $path = new Path();

        $this->assertTrue(Undefined::isDefault($path->format, $path->enum, $path->description));
    }

    public function testPropertyUsesItsDefaults(): void
    {
        $property = new IdProperty();

        $this->assertSame('uuid', $property->format);
        $this->assertSame(['read'], $property->groups);
        $this->assertSame(3, $property->multipleOf);
        $this->assertInstanceOf(Schema::class, $property->not);

        $property = new IdProperty(format: 'ulid', groups: ['write'], multipleOf: 2);

        $this->assertSame('ulid', $property->format);
        $this->assertSame(['write'], $property->groups);
        $this->assertSame(2, $property->multipleOf);
    }

    public function testPropertyWithoutDefaults(): void
    {
        $property = new Property();

        $this->assertNull($property->groups);
        $this->assertTrue(Undefined::isDefault($property->multipleOf, $property->description, $property->example, $property->default));
        $this->assertContains('groups', Property::$_blacklist);
    }

    private function sampleOf(\ReflectionParameter $parameter): mixed
    {
        $types = explode('|', (string) $parameter->getType());

        return match (true) {
            \in_array('mixed', $types, true) => 'mixed',
            \in_array('string', $types, true) => 'foo',
            \in_array('bool', $types, true) => false,
            \in_array('int', $types, true) => 1,
            \in_array('array', $types, true) => ['foo'],
            default => (new \ReflectionClass($types[0]))->newInstanceWithoutConstructor(),
        };
    }
}

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class IdPath extends Path
{
    public static function defaults(): PathDefaults
    {
        return PathDefaults::create()
            ->name('id')
            ->description('The identifier')
            ->required()
            ->format('uuid')
            ->enum(['a']);
    }
}

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class IdProperty extends Property
{
    public static function defaults(): PropertyDefaults
    {
        return PropertyDefaults::create()
            ->format('uuid')
            ->groups(['read'])
            ->multipleOf(3)
            ->not(new Schema());
    }
}
