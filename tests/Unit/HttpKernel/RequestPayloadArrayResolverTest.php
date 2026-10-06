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

use OpenSolid\OpenApiBundle\Attribute\Payload;
use OpenSolid\OpenApiBundle\Attribute\Query;
use OpenSolid\OpenApiBundle\HttpKernel\Controller\ArgumentResolver\RequestPayloadArrayResolver;
use OpenSolid\Tests\OpenApiBundle\Unit\HttpKernel\Fixtures\Dummy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestPayloadValueResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

class RequestPayloadArrayResolverTest extends TestCase
{
    public function testResolveDelegatesArgumentsWithoutMappingAttribute(): void
    {
        $resolver = $this->createResolver();

        $this->assertSame([], $resolver->resolve(new Request(), new ArgumentMetadata('foo', 'string', false, false, null)));
    }

    public function testResolveRejectsVariadicArguments(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Mapping variadic argument "$foo" is not supported.');

        $this->createResolver()->resolve(new Request(), new ArgumentMetadata('foo', Dummy::class, true, false, null, attributes: [new Payload()]));
    }

    public function testResolveKeepsArrayTypeWithoutItemsType(): void
    {
        $attribute = new MapRequestPayload();
        $result = $this->createResolver()->resolve(new Request(), new ArgumentMetadata('foo', 'array', false, false, null, attributes: [$attribute]));

        $this->assertSame([$attribute], $result);
        $this->assertSame('array', $attribute->metadata->getType());
    }

    public function testResolveUsesTheNativeTypeAsItemsType(): void
    {
        $attribute = new MapRequestPayload(type: Dummy::class);
        $this->createResolver()->resolve(new Request(), new ArgumentMetadata('foo', 'array', false, true, [], attributes: [$attribute]));

        $this->assertSame(Dummy::class.'[]', $attribute->metadata->getType());
        $this->assertSame([], $attribute->metadata->getDefaultValue());
    }

    public function testSubscribedEvents(): void
    {
        $this->assertArrayHasKey(KernelEvents::CONTROLLER_ARGUMENTS, RequestPayloadArrayResolver::getSubscribedEvents());
    }

    public function testArgumentsWithoutMappingAttributeAreKept(): void
    {
        $event = $this->createEvent(new Request(), ['foo']);
        $this->createResolver()->onKernelControllerArguments($event);

        $this->assertSame(['foo'], $event->getArguments());
    }

    public function testUntypedArgumentIsRejected(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Could not resolve the "$foo" controller argument: argument should be typed.');

        $this->resolve(new Request(), new Payload(), new ArgumentMetadata('foo', null, false, false, null));
    }

    public function testMapRequestPayload(): void
    {
        $payload = $this->resolve($this->jsonRequest('{"name": "foo"}'), new Payload(), $this->metadata());

        $this->assertInstanceOf(Dummy::class, $payload);
        $this->assertSame('foo', $payload->name);
    }

    public function testMapRequestPayloadFromFormData(): void
    {
        $request = new Request(request: ['name' => 'foo'], server: ['CONTENT_TYPE' => 'application/x-www-form-urlencoded']);
        $payload = $this->resolve($request, new Payload(), $this->metadata());

        $this->assertSame('foo', $payload->name);
    }

    public function testMapQueryStringWithKey(): void
    {
        $request = new Request(query: ['filter' => ['name' => 'foo']]);
        $payload = $this->resolve($request, new Query(key: 'filter'), $this->metadata());

        $this->assertSame('foo', $payload->name);
    }

    public function testEmptyQueryStringUsesTheDefaultValue(): void
    {
        $default = new Dummy();
        $payload = $this->resolve(new Request(), new Query(), new ArgumentMetadata('foo', Dummy::class, false, true, $default));

        $this->assertSame($default, $payload);
    }

    public function testEmptyPayloadOfNullableArgumentIsNull(): void
    {
        $payload = $this->resolve($this->jsonRequest(''), new Payload(), new ArgumentMetadata('foo', Dummy::class, false, false, null, true));

        $this->assertNull($payload);
    }

    public function testEmptyPayloadOfRequiredArgumentIsRejected(): void
    {
        $this->assertHttpException(422, null, fn () => $this->resolve($this->jsonRequest(''), new Payload(), $this->metadata()));
    }

    public function testValidationFailure(): void
    {
        try {
            $this->resolve($this->jsonRequest('{"name": "fo"}'), new Payload(), $this->metadata());
            $this->fail('An HttpException was expected.');
        } catch (HttpException $e) {
            $this->assertSame(422, $e->getStatusCode());
            $this->assertInstanceOf(ValidationFailedException::class, $e->getPrevious());
        }
    }

    public function testDenormalizationErrorsBecomeViolations(): void
    {
        try {
            $this->resolve($this->jsonRequest('{"name": 1, "status": "unknown"}'), new Payload(), $this->metadata());
            $this->fail('An HttpException was expected.');
        } catch (HttpException $e) {
            $this->assertSame(422, $e->getStatusCode());
            $violations = $e->getPrevious()->getViolations();
            $this->assertCount(2, $violations);
            $this->assertSame('name', $violations[0]->getPropertyPath());
            $this->assertSame('This value should be of type string.', $violations[0]->getMessage());
            $this->assertSame('status', $violations[1]->getPropertyPath());
            $this->assertArrayHasKey('hint', $violations[1]->getParameters());
        }
    }

    public function testDenormalizationErrorsWithoutValidator(): void
    {
        $this->assertHttpException(422, null, fn () => $this->resolve($this->jsonRequest('{"name": 1}'), new Payload(), $this->metadata(), $this->createResolver(validator: false)));
    }

    public function testPayloadWithoutValidator(): void
    {
        $payload = $this->resolve($this->jsonRequest('{"name": "fo"}'), new Payload(), $this->metadata(), $this->createResolver(validator: false));

        $this->assertSame('fo', $payload->name);
    }

    public function testMissingContentTypeIsUnsupported(): void
    {
        $this->assertHttpException(415, 'Unsupported format.', fn () => $this->resolve(new Request(content: '{}'), new Payload(), $this->metadata()));
    }

    public function testNotAcceptedFormatIsUnsupported(): void
    {
        $this->assertHttpException(415, 'Unsupported format, expects "xml", but "json" given.', fn () => $this->resolve($this->jsonRequest('{}'), new Payload(acceptFormat: 'xml'), $this->metadata()));
    }

    public function testFormatWithoutDecoderIsUnsupported(): void
    {
        $this->assertHttpException(415, 'Unsupported format: "xml".', fn () => $this->resolve(new Request(server: ['CONTENT_TYPE' => 'application/xml'], content: '<dummy/>'), new Payload(), $this->metadata()));
    }

    public function testInvalidFormData(): void
    {
        $this->assertHttpException(400, 'Request payload contains invalid "form" data.', fn () => $this->resolve(new Request(server: ['CONTENT_TYPE' => 'application/x-www-form-urlencoded'], content: 'name=foo'), new Payload(), $this->metadata()));
    }

    public function testInvalidJson(): void
    {
        $this->assertHttpException(400, 'Request payload contains invalid "json" data.', fn () => $this->resolve($this->jsonRequest('{"name": '), new Payload(), $this->metadata()));
    }

    private function resolve(Request $request, MapRequestPayload|Query $attribute, ArgumentMetadata $metadata, ?RequestPayloadArrayResolver $resolver = null): mixed
    {
        $resolver ??= $this->createResolver();
        $attribute->metadata = $metadata;
        $event = $this->createEvent($request, [$attribute]);
        $resolver->onKernelControllerArguments($event);

        return $event->getArguments()[0];
    }

    private function metadata(): ArgumentMetadata
    {
        return new ArgumentMetadata('foo', Dummy::class, false, false, null);
    }

    private function jsonRequest(string $content): Request
    {
        return new Request(server: ['CONTENT_TYPE' => 'application/json'], content: $content);
    }

    private function assertHttpException(int $statusCode, ?string $message, \Closure $resolve): void
    {
        try {
            $resolve();
            $this->fail('An HttpException was expected.');
        } catch (HttpException $e) {
            $this->assertSame($statusCode, $e->getStatusCode());
            if (null !== $message) {
                $this->assertSame($message, $e->getMessage());
            }
        }
    }

    private function createEvent(Request $request, array $arguments): ControllerArgumentsEvent
    {
        return new ControllerArgumentsEvent($this->createStub(HttpKernelInterface::class), static fn () => null, $arguments, $request, HttpKernelInterface::MAIN_REQUEST);
    }

    private function createResolver(bool $validator = true): RequestPayloadArrayResolver
    {
        $serializer = new Serializer([new BackedEnumNormalizer(), new ArrayDenormalizer(), new ObjectNormalizer(propertyTypeExtractor: new ReflectionExtractor())], [new JsonEncoder()]);
        $validatorService = $validator ? Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator() : null;

        return new RequestPayloadArrayResolver($serializer, $validatorService, null, new RequestPayloadValueResolver($serializer, $validatorService));
    }
}
