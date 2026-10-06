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

use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;
use OpenApi\Context;
use OpenApi\Undefined;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\Operation\OperationQueryParameterGuesser;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\Operation\OperationRequestBodyGuesser;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\Operation\OperationResponseGuesser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class GuessersTest extends TestCase
{
    public function testGuessersIgnoreOtherReflectorsAndAnnotations(): void
    {
        $schema = new OAT\Schema();

        foreach ([new OperationQueryParameterGuesser(), new OperationRequestBodyGuesser(), new OperationResponseGuesser()] as $guesser) {
            $guesser->guess(new \ReflectionClass(GuesserFixture::class), new OAT\Get(), new Context());
            $guesser->guess(new \ReflectionMethod(GuesserFixture::class, 'scalarQuery'), $schema, new Context());
        }

        $this->assertTrue(Undefined::isDefault($schema->properties));
    }

    public function testQueryStringOfScalarTypeIsIgnored(): void
    {
        $operation = new OAT\Get();
        (new OperationQueryParameterGuesser())->guess(new \ReflectionMethod(GuesserFixture::class, 'scalarQuery'), $operation, new Context());

        $this->assertTrue(Undefined::isDefault($operation->parameters));
    }

    public function testDuplicatedQueryParametersAreIgnored(): void
    {
        $operation = new OAT\Get();
        (new OperationQueryParameterGuesser())->guess(new \ReflectionMethod(GuesserFixture::class, 'duplicatedQuery'), $operation, new Context());

        $this->assertSame(['name', 'untyped'], array_map(static fn (OA\Parameter $p) => $p->name, $operation->parameters));
    }

    public function testDeclaredRequestBodyIsKept(): void
    {
        $requestBody = new OAT\RequestBody(description: 'Declared');
        $operation = new OAT\Post(requestBody: $requestBody);
        (new OperationRequestBodyGuesser())->guess(new \ReflectionMethod(GuesserFixture::class, 'unionPayload'), $operation, new Context());

        $this->assertSame($requestBody, $operation->requestBody);
    }

    public function testRequestBodyIsOnlyGuessedForWriteOperations(): void
    {
        $operation = new OAT\Get();
        (new OperationRequestBodyGuesser())->guess(new \ReflectionMethod(GuesserFixture::class, 'unionPayload'), $operation, new Context());

        $this->assertTrue(Undefined::isDefault($operation->requestBody));
    }

    public function testUnionTypedPayloadIsIgnored(): void
    {
        $operation = new OAT\Post();
        (new OperationRequestBodyGuesser())->guess(new \ReflectionMethod(GuesserFixture::class, 'unionPayload'), $operation, new Context());

        $this->assertTrue(Undefined::isDefault($operation->requestBody));
    }

    public function testDeclaredResponseWithBodyIsKept(): void
    {
        $operation = new OAT\Get(responses: [
            $ok = new OAT\Response(response: 200, ref: '#/components/responses/Ok'),
            $text = new OAT\Response(response: 'default', description: 'Text', content: new OAT\MediaType(mediaType: 'text/plain')),
        ]);
        (new OperationResponseGuesser())->guess(new \ReflectionMethod(GuesserFixture::class, 'object'), $operation, new Context());

        $this->assertSame([$ok, $text], $operation->responses);
        $this->assertSame([], $ok->_unmerged);
    }
}

class GuesserFixture
{
    public function scalarQuery(#[MapQueryString] string $query): void
    {
    }

    public function duplicatedQuery(#[MapQueryParameter] ?string $name = null, #[MapQueryString] ?GuesserQuery $query = null, #[MapQueryParameter(name: 'name')] ?string $other = null): void
    {
    }

    public function unionPayload(#[MapRequestPayload] GuesserQuery|\stdClass $payload): void
    {
    }

    public function object(): \stdClass
    {
        return new \stdClass();
    }
}

class GuesserQuery
{
    #[OAT\QueryParameter]
    public ?string $name = null;

    #[OAT\QueryParameter]
    public $untyped;

    public $ignored;
}
