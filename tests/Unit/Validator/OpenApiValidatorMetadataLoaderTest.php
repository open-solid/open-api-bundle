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

namespace OpenSolid\Tests\OpenApiBundle\Unit\Validator;

use OpenSolid\OpenApiBundle\Attribute\Property;
use OpenSolid\OpenApiBundle\Validator\Mapping\Loader\FormatMetadataLoader;
use OpenSolid\OpenApiBundle\Validator\Mapping\Loader\OpenApiValidatorMetadataLoader;
use OpenSolid\OpenApiBundle\Validator\Mapping\Loader\ValidationMetadataLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\CascadingStrategy;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class OpenApiValidatorMetadataLoaderTest extends TestCase
{
    public function testClassWithoutPropertyAttributes(): void
    {
        $metadata = new ClassMetadata(\stdClass::class);

        $this->assertFalse($this->createLoader()->loadClassMetadata($metadata));
    }

    public function testFormats(): void
    {
        $this->assertConstraints([
            'uuid' => [Assert\Uuid::class, Assert\NotNull::class],
            'email' => [Assert\Email::class, Assert\NotNull::class],
            'password' => [Assert\PasswordStrength::class, Assert\NotNull::class],
            'date' => [Assert\Date::class, Assert\NotNull::class],
            'dateTime' => [Assert\DateTime::class, Assert\NotNull::class],
            'currency' => [Assert\Currency::class, Assert\NotNull::class],
            'other' => [Assert\NotNull::class],
        ], FormatFixture::class);
    }

    public function testKeywords(): void
    {
        $this->assertConstraints([
            'length' => [Assert\Length::class],
            'minLength' => [Assert\Length::class],
            'count' => [Assert\Count::class],
            'maxItems' => [Assert\Count::class],
            'minimum' => [Assert\GreaterThanOrEqual::class],
            'exclusiveMinimumFlag' => [Assert\GreaterThan::class],
            'exclusiveMinimumValue' => [Assert\GreaterThan::class],
            'maximum' => [Assert\LessThanOrEqual::class],
            'exclusiveMaximumFlag' => [Assert\LessThan::class],
            'exclusiveMaximumValue' => [Assert\LessThan::class],
            'pattern' => [Assert\Regex::class],
            'unique' => [Assert\Unique::class],
            'enumClass' => [Assert\Choice::class],
            'enumValues' => [Assert\Choice::class],
            'emptyEnum' => [],
            'multipleOf' => [Assert\DivisibleBy::class],
            'const' => [Assert\EqualTo::class],
            'object' => [Assert\NotNull::class],
            'untyped' => [],
        ], KeywordFixture::class);
    }

    public function testConstraintValues(): void
    {
        $metadata = new ClassMetadata(KeywordFixture::class);
        $this->createLoader()->loadClassMetadata($metadata);

        $this->assertSame(['draft', 'Foo', 'bar'], $this->constraintsOf($metadata, 'enumValues')[0]->choices);
        $this->assertSame(['draft'], $this->constraintsOf($metadata, 'enumClass')[0]->choices);
        $this->assertSame(5, $this->constraintsOf($metadata, 'exclusiveMinimumValue')[0]->value);
        $this->assertSame(1, $this->constraintsOf($metadata, 'exclusiveMinimumFlag')[0]->value);
        $this->assertSame(['Default', 'KeywordFixture'], $this->constraintsOf($metadata, 'length')[0]->groups);
        $this->assertSame(['create'], $this->constraintsOf($metadata, 'minLength')[0]->groups);
        $this->assertSame(CascadingStrategy::CASCADE, $metadata->getPropertyMetadata('object')[0]->getCascadingStrategy());
    }

    private function assertConstraints(array $expected, string $class): void
    {
        $metadata = new ClassMetadata($class);

        $this->assertTrue($this->createLoader()->loadClassMetadata($metadata));

        foreach ($expected as $property => $constraints) {
            $this->assertSame($constraints, array_map(static fn (Constraint $c): string => $c::class, $this->constraintsOf($metadata, $property)), $property);
        }
    }

    /**
     * @return list<Constraint>
     */
    private function constraintsOf(ClassMetadata $metadata, string $property): array
    {
        $constraints = [];
        foreach ($metadata->getPropertyMetadata($property) as $propertyMetadata) {
            array_push($constraints, ...$propertyMetadata->getConstraints());
        }

        return $constraints;
    }

    private function createLoader(): OpenApiValidatorMetadataLoader
    {
        return new OpenApiValidatorMetadataLoader([new FormatMetadataLoader(), new ValidationMetadataLoader()]);
    }
}

enum FixtureStatus: string
{
    case Draft = 'draft';
}

enum FixturePure
{
    case Foo;
}

class FormatFixture
{
    #[Property(format: 'uuid')]
    public string $uuid;

    #[Property(format: 'email')]
    public string $email;

    #[Property(format: 'password')]
    public string $password;

    #[Property(format: 'date')]
    public string $date;

    #[Property(format: 'date-time')]
    public string $dateTime;

    #[Property(format: 'currency')]
    public string $currency;

    #[Property(format: 'other')]
    public string $other;
}

class KeywordFixture
{
    #[Property(maxLength: 10)]
    public ?string $length = null;

    #[Property(minLength: 1, groups: ['create'])]
    public ?string $minLength = null;

    #[Property(minItems: 1)]
    public ?array $count = null;

    #[Property(maxItems: 1)]
    public ?array $maxItems = null;

    #[Property(minimum: 1, exclusiveMinimum: false)]
    public ?int $minimum = null;

    #[Property(minimum: 1, exclusiveMinimum: true)]
    public ?int $exclusiveMinimumFlag = null;

    #[Property(exclusiveMinimum: 5)]
    public ?int $exclusiveMinimumValue = null;

    #[Property(maximum: 1)]
    public ?int $maximum = null;

    #[Property(maximum: 1, exclusiveMaximum: true)]
    public ?int $exclusiveMaximumFlag = null;

    #[Property(exclusiveMaximum: 5.5)]
    public ?float $exclusiveMaximumValue = null;

    #[Property(pattern: '/^a/')]
    public ?string $pattern = null;

    #[Property(uniqueItems: true)]
    public ?array $unique = null;

    #[Property(enum: FixtureStatus::class)]
    public ?string $enumClass = null;

    #[Property(enum: [FixtureStatus::Draft, FixturePure::Foo, 'bar'])]
    public ?string $enumValues = null;

    #[Property(enum: [])]
    public ?string $emptyEnum = null;

    #[Property(multipleOf: 5)]
    public ?int $multipleOf = null;

    #[Property(const: 'foo')]
    public ?string $const = null;

    #[Property]
    public FixtureObject $object;

    #[Property]
    public $untyped;
}

class FixtureObject
{
}
