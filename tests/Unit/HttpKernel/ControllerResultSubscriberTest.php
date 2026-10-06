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

use OpenApi\Attributes as OA;
use OpenSolid\OpenApiBundle\HttpKernel\Controller\ControllerResultSubscriber;
use OpenSolid\OpenApiBundle\Routing\Attribute\Get;
use OpenSolid\OpenApiBundle\Routing\Attribute\Post;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsMetadata;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ControllerResultSubscriberTest extends TestCase
{
    public function testSubscribedEvents(): void
    {
        $this->assertSame([KernelEvents::VIEW => 'onKernelView'], ControllerResultSubscriber::getSubscribedEvents());
    }

    public function testSubRequestIsIgnored(): void
    {
        $event = $this->createEvent(['foo'], type: HttpKernelInterface::SUB_REQUEST);
        $this->createSubscriber()->onKernelView($event);

        $this->assertNull($event->getResponse());
    }

    public function testResponseResultIsIgnored(): void
    {
        $event = $this->createEvent(new Response());
        $this->createSubscriber()->onKernelView($event);

        $this->assertNull($event->getResponse());
    }

    public function testResultWithoutControllerMetadataIsIgnored(): void
    {
        $event = $this->createEvent(['foo'], withMetadata: false);
        $this->createSubscriber()->onKernelView($event);

        $this->assertNull($event->getResponse());
    }

    public function testNullResultWithoutControllerMetadata(): void
    {
        $event = $this->createEvent(null, withMetadata: false);
        $this->createSubscriber()->onKernelView($event);

        $this->assertSame(204, $event->getResponse()->getStatusCode());
    }

    public function testStatusCodes(): void
    {
        $this->assertSame(200, $this->statusCodeOf([new \stdClass()]));
        $this->assertSame(201, $this->statusCodeOf([new Post('/foo')]));
        $this->assertSame(202, $this->statusCodeOf([new Post('/foo', statusCode: 202)]));
        $this->assertSame(203, $this->statusCodeOf([new Get('/foo', responses: [new OA\Response(response: 'default'), new OA\Response(response: 400), new OA\Response(response: 203)])]));
        $this->assertSame(200, $this->statusCodeOf([new Get('/foo')]));
        $this->assertSame(204, $this->statusCodeOf([new Get('/foo')], null));
        $this->assertSame(205, $this->statusCodeOf([new Get('/foo', statusCode: 205)], null));
    }

    public function testResponseContent(): void
    {
        $event = $this->createEvent(['foo' => 'bar'], [new Get('/foo')], request: new Request(server: ['HTTP_ACCEPT' => 'application/json']));
        $this->createSubscriber()->onKernelView($event);

        $this->assertSame('{"foo":"bar"}', $event->getResponse()->getContent());
        $this->assertSame('application/json', $event->getResponse()->headers->get('Content-Type'));
    }

    private function statusCodeOf(array $attributes, mixed $result = ['foo']): int
    {
        $event = $this->createEvent($result, $attributes);
        $this->createSubscriber()->onKernelView($event);

        return $event->getResponse()->getStatusCode();
    }

    private function createEvent(mixed $result, array $attributes = [], int $type = HttpKernelInterface::MAIN_REQUEST, bool $withMetadata = true, ?Request $request = null): ViewEvent
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $request ??= new Request();
        $metadata = null;

        if ($withMetadata) {
            $controllerEvent = new ControllerEvent($kernel, static fn () => null, $request, $type);

            if (class_exists(ControllerArgumentsMetadata::class)) {
                $controllerEvent->setController(static fn () => null, $attributes);
                $metadata = new ControllerArgumentsMetadata($controllerEvent, new ControllerArgumentsEvent($kernel, $controllerEvent, [], $request, $type));
            } else {
                $grouped = [];
                foreach ($attributes as $attribute) {
                    $grouped[$attribute::class][] = $attribute;
                }
                $controllerEvent->setController(static fn () => null, $grouped);
                $metadata = new ControllerArgumentsEvent($kernel, $controllerEvent, [], $request, $type);
            }
        }

        return new ViewEvent($kernel, $request, $type, $result, $metadata);
    }

    private function createSubscriber(): ControllerResultSubscriber
    {
        return new ControllerResultSubscriber(new Serializer([new ObjectNormalizer()], [new JsonEncoder()]));
    }
}
