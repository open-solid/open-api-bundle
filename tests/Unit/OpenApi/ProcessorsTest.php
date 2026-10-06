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

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;
use OpenApi\Context;
use OpenApi\Generator;
use OpenApi\Serializer;
use OpenApi\Undefined;
use OpenSolid\OpenApiBundle\Attribute\Path;
use OpenSolid\OpenApiBundle\OpenApi\Constraint\ConstraintSchemaApplier;
use OpenSolid\OpenApiBundle\OpenApi\Processor\AugmentEnumSchemas;
use OpenSolid\OpenApiBundle\OpenApi\Processor\AugmentParameterConstraints;
use OpenSolid\OpenApiBundle\OpenApi\Processor\AugmentPathParameters;
use OpenSolid\OpenApiBundle\OpenApi\Processor\AugmentQueryParameters;
use OpenSolid\OpenApiBundle\OpenApi\Processor\AugmentSchemas;
use OpenSolid\OpenApiBundle\OpenApi\Processor\CleanupAnnotations;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Validator\Constraints as Assert;

class ProcessorsTest extends TestCase
{
    public function testCleanupWithoutOpenApi(): void
    {
        $analysis = new Analysis([], $this->context());
        (new CleanupAnnotations())($analysis);

        $this->assertNull($analysis->openapi);
    }

    public function testCleanupWithoutComponents(): void
    {
        $analysis = $this->analysis('{"openapi": "3.1.0", "info": {"title": "t", "version": "1"}}');
        (new CleanupAnnotations())($analysis);

        $this->assertTrue(Undefined::isDefault($analysis->openapi->components));
    }

    public function testCleanupWithEmptyComponents(): void
    {
        $analysis = $this->analysis('{"openapi": "3.1.0", "info": {"title": "t", "version": "1"}, "components": {"securitySchemes": {"bearer": {"type": "http", "scheme": "bearer"}}}}');
        (new CleanupAnnotations())($analysis);

        $this->assertTrue(Undefined::isDefault($analysis->openapi->components->schemas, $analysis->openapi->components->responses, $analysis->openapi->components->parameters));
    }

    public function testCleanupRemovesUnusedComponents(): void
    {
        $analysis = $this->analysis(<<<'JSON'
            {
                "openapi": "3.1.0",
                "info": {"title": "t", "version": "1"},
                "paths": {
                    "/foo": {
                        "get": {
                            "responses": {
                                "200": {"description": "ok", "content": {"application/json": {"schema": {"$ref": "#/components/schemas/Used"}}}},
                                "400": {"$ref": "#/components/responses/400"}
                            }
                        },
                        "post": {}
                    }
                },
                "components": {
                    "schemas": {
                        "Used": {"type": "object", "properties": {"nested": {"$ref": "#/components/schemas/Nested"}}},
                        "Nested": {"type": "string"},
                        "Unused": {"type": "object", "properties": {"other": {"$ref": "#/components/schemas/OnlyUsedByUnused"}}},
                        "OnlyUsedByUnused": {"type": "string"},
                        "CycleA": {"type": "object", "properties": {"b": {"$ref": "#/components/schemas/CycleB"}}},
                        "CycleB": {"type": "object", "properties": {"a": {"$ref": "#/components/schemas/CycleA"}}}
                    },
                    "responses": {
                        "400": {"description": "Bad request"},
                        "418": {"description": "Teapot"}
                    },
                    "parameters": {
                        "named": {"name": "named", "in": "query"}
                    }
                }
            }
            JSON);
        $components = $analysis->openapi->components;

        // a duplicated response, a nameless parameter and a schema shared twice
        $components->responses[] = $duplicated = new OA\Response(['response' => 400, 'description' => 'Duplicated', '_context' => $this->context()]);
        $components->parameters[] = $nameless = new OA\Parameter(['in' => 'query', '_context' => $this->context()]);
        $shared = new OA\Schema(['ref' => '#/components/schemas/Nested', '_context' => $this->context()]);
        $used = array_values(array_filter($components->schemas, static fn (OA\Schema $s) => 'Used' === $s->schema))[0];
        $used->allOf = [$shared, $shared];
        $analysis->addAnnotations([$duplicated, $nameless, $shared], $this->context());

        (new CleanupAnnotations())($analysis);

        $this->assertSame(['Used', 'Nested'], array_values(array_map(static fn (OA\Schema $s) => $s->schema, $components->schemas)));
        $this->assertSame(['Bad request'], array_values(array_map(static fn (OA\Response $r) => $r->description, $components->responses)));
        $this->assertSame(['named'], array_values(array_map(static fn (OA\Parameter $p) => $p->name, $components->parameters)));
        $this->assertFalse($analysis->annotations->offsetExists($duplicated));
        $this->assertFalse($analysis->annotations->offsetExists($nameless));
    }

    public function testCleanupRemovesAllUnusedComponents(): void
    {
        $analysis = $this->analysis('{"openapi": "3.1.0", "info": {"title": "t", "version": "1"}, "components": {"schemas": {"Unused": {"type": "string"}}, "responses": {"400": {"description": "Bad"}}}}');
        $analysis->openapi->components->parameters = [$nameless = new OA\Parameter(['in' => 'query', '_context' => $this->context()])];
        $analysis->addAnnotation($nameless, $this->context());

        (new CleanupAnnotations())($analysis);

        $components = $analysis->openapi->components;
        $this->assertTrue(Undefined::isDefault($components->schemas, $components->responses, $components->parameters));
    }

    public function testAugmentPathParameters(): void
    {
        $parameters = [
            'class' => new Path(name: 'class', enum: ProcessorStatus::class),
            'values' => new Path(name: 'values', enum: ['x', ProcessorStatus::Draft, ProcessorPure::Foo]),
            'string' => new Path(name: 'string', enum: 'single'),
            'explicit' => new Path(name: 'explicit', schema: new OAT\Schema(format: 'ulid', enum: ['a']), format: 'uuid', enum: ['b']),
            'none' => new Path(name: 'none'),
        ];
        (new AugmentPathParameters())(new Analysis(array_values($parameters), $this->context()));

        $this->assertSame(['draft'], $parameters['class']->schema->enum);
        $this->assertSame(['x', 'draft', 'Foo'], $parameters['values']->schema->enum);
        $this->assertSame(['single'], $parameters['string']->schema->enum);
        $this->assertSame('ulid', $parameters['explicit']->schema->format);
        $this->assertSame(['a'], $parameters['explicit']->schema->enum);
        $this->assertTrue(Undefined::isDefault($parameters['none']->schema));
    }

    public function testAugmentQueryParametersIgnoresParametersWithoutReflector(): void
    {
        $parameter = new OAT\QueryParameter(name: 'q');
        $parameter->_context = new Context(['reflector' => new \ReflectionMethod(self::class, 'context')], $this->context());
        (new AugmentQueryParameters())->setGenerator(new Generator())(new Analysis([$parameter], $this->context()));

        $this->assertTrue(Undefined::isDefault($parameter->schema));
    }

    public function testAugmentQueryParametersNormalizesEnumDefaults(): void
    {
        $pure = $this->queryParameterOf('pure');
        $backed = $this->queryParameterOf('backed');
        (new AugmentQueryParameters())->setGenerator(new Generator())(new Analysis([$pure, $backed], $this->context()));

        $this->assertSame('Foo', $pure->schema->default);
        $this->assertSame('draft', $backed->schema->default);
    }

    public function testAugmentQueryParametersKeepsExplicitRequiredOnProperties(): void
    {
        $explicit = $this->queryParameterOf('optional', required: true);
        $inferred = $this->queryParameterOf('optional');
        (new AugmentQueryParameters())->setGenerator(new Generator())(new Analysis([$explicit, $inferred], $this->context()));

        $this->assertTrue($explicit->required);
        $this->assertFalse($inferred->required);
    }

    public function testAugmentParameterConstraintsMarksConstrainedParametersAsRequired(): void
    {
        $parameter = new OAT\QueryParameter(name: 'single', required: false);
        $parameter->_context = new Context(['reflector' => new \ReflectionProperty(ProcessorFixture::class, 'single')], $this->context());
        (new AugmentParameterConstraints())(new Analysis([$parameter], $this->context()));

        $this->assertTrue($parameter->required);
    }

    public function testAugmentEnumSchemas(): void
    {
        $withoutSchema = new OAT\QueryParameter(name: 'q');
        $withoutReflector = new OA\Schema(['_context' => $this->context()]);
        $nullableEnum = $this->schemaOf('size');
        $className = new OA\Schema(['type' => \stdClass::class, '_context' => $this->context()]);
        $customType = new OA\Schema(['type' => 'custom', '_context' => $this->context()]);
        $enumItems = new OA\Schema(['type' => 'array', 'items' => new OA\Items(['type' => ProcessorStatus::class, '_context' => $this->context()]), '_context' => $this->context()]);

        (new AugmentEnumSchemas())->setGenerator(new Generator())(new Analysis([$withoutSchema, $withoutReflector, $nullableEnum, $className, $customType, $enumItems], $this->context()));

        $this->assertTrue(Undefined::isDefault($withoutSchema->schema, $withoutReflector->type));
        $this->assertTrue($nullableEnum->nullable);
        $this->assertSame([1, 2, null], $nullableEnum->enum);
        $this->assertSame('integer', $nullableEnum->type);
        $this->assertTrue(Undefined::isDefault($className->type));
        $this->assertSame('custom', $customType->type);
        $this->assertSame(['draft'], $enumItems->items->enum);
        $this->assertSame('string', $enumItems->items->type);
    }

    public function testAugmentSchemasDeniesNullForMandatoryFields(): void
    {
        $schema = new OA\Schema(['properties' => [
            $single = $this->propertyOf('single', ['string', 'null']),
            $onlyNull = $this->propertyOf('onlyNull', ['null']),
            $many = $this->propertyOf('many', ['string', 'integer', 'null']),
        ], '_context' => $this->context()]);

        (new AugmentSchemas(new ConstraintSchemaApplier()))(new Analysis([$schema], $this->context()));

        $this->assertSame(['single', 'onlyNull', 'many'], $schema->required);
        $this->assertSame('string', $single->type);
        $this->assertTrue(Undefined::isDefault($onlyNull->type));
        $this->assertSame(['string', 'integer'], $many->type);
        $this->assertFalse($single->nullable);
    }

    public function testConstraintApplierEdgeCases(): void
    {
        $applier = new ConstraintSchemaApplier();

        $schema = new OA\Schema(['type' => 'string', '_context' => $this->context()]);
        $applier->apply($schema, new \ReflectionProperty(ProcessorFixture::class, 'notAnArray'));
        $this->assertTrue(Undefined::isDefault($schema->items));

        $schema = new OA\Schema(['_context' => $this->context()]);
        $applier->apply($schema, new \ReflectionProperty(ProcessorFixture::class, 'emptyPattern'));
        $this->assertTrue(Undefined::isDefault($schema->pattern));

        $this->assertNull(ConstraintSchemaApplier::resolveReflector(new \ReflectionClass(self::class)));
    }

    private function queryParameterOf(string $property, ?bool $required = null): OAT\QueryParameter
    {
        $parameter = new OAT\QueryParameter(name: $property, required: $required);
        $parameter->_context = new Context(['reflector' => new \ReflectionProperty(ProcessorQuery::class, $property)], $this->context());

        return $parameter;
    }

    private function schemaOf(string $property): OA\Schema
    {
        return new OA\Schema(['_context' => new Context(['reflector' => new \ReflectionProperty(ProcessorFixture::class, $property)], $this->context())]);
    }

    private function propertyOf(string $property, array $type): OA\Property
    {
        return new OA\Property(['property' => $property, 'type' => $type, '_context' => new Context(['reflector' => new \ReflectionProperty(ProcessorFixture::class, $property)], $this->context())]);
    }

    private function analysis(string $json): Analysis
    {
        $openapi = (new Serializer())->deserialize($json, OA\OpenApi::class);

        return new Analysis([$openapi], $this->context());
    }

    private function context(): Context
    {
        return new Context(['logger' => new NullLogger()]);
    }
}

enum ProcessorStatus: string
{
    case Draft = 'draft';
}

enum ProcessorSize: int
{
    case Small = 1;
    case Large = 2;
}

enum ProcessorPure
{
    case Foo;
}

class ProcessorFixture
{
    public ?ProcessorSize $size = null;

    #[Assert\NotNull]
    public ?string $single = null;

    #[Assert\NotNull]
    public ?string $onlyNull = null;

    #[Assert\NotNull]
    public string|int|null $many = null;

    #[Assert\All([new Assert\Positive()])]
    public mixed $notAnArray = null;

    #[Assert\Regex('')]
    public ?string $emptyPattern = null;
}

class ProcessorQuery
{
    public ProcessorPure $pure = ProcessorPure::Foo;

    public ProcessorStatus $backed = ProcessorStatus::Draft;

    public ?string $optional = null;
}
