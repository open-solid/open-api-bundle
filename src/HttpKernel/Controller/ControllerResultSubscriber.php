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

namespace OpenSolid\OpenApiBundle\HttpKernel\Controller;

use OpenApi\Annotations\Operation;
use OpenApi\Attributes as OA;
use OpenApi\Undefined;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

readonly class ControllerResultSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SerializerInterface $serializer,
    ) {
    }

    public function onKernelView(ViewEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $result = $event->getControllerResult();

        if ($result instanceof Response) {
            return;
        }

        // Symfony 8.1+ exposes "controllerMetadata", older versions the deprecated "controllerArgumentsEvent"
        $controllerMetadata = property_exists($event, 'controllerMetadata') ? $event->controllerMetadata : $event->controllerArgumentsEvent;
        $controllerAttributes = $controllerMetadata?->getAttributes() ?? [];

        if (null === $result) {
            $event->setResponse(new Response(status: $this->guessStatusCode($controllerAttributes, 204)));

            return;
        }

        if (null === $controllerMetadata) {
            return;
        }

        $request = $event->getRequest();
        $content = $this->serializer->serialize($result, $request->getPreferredFormat('json'));
        $statusCode = $this->guessStatusCode($controllerAttributes);
        $contentType = $request->getAcceptableContentTypes()[0] ?? $request->headers->get('CONTENT_TYPE', 'application/json');

        $event->setResponse(new Response($content, $statusCode, ['Content-Type' => $contentType]));
    }

    /**
     * @param array<class-string, list<object>> $controllerAttributes
     */
    protected function guessStatusCode(array $controllerAttributes, ?int $default = null): int
    {
        foreach ($controllerAttributes as $attributes) {
            foreach ($attributes as $attribute) {
                if (!$attribute instanceof Operation) {
                    continue;
                }

                if (property_exists($attribute, 'statusCode') && null !== $attribute->statusCode) {
                    return $attribute->statusCode;
                }

                if (null !== $default) {
                    return $default;
                }

                if (!Undefined::isDefault($attribute->responses)) {
                    foreach ($attribute->responses as $res) {
                        if (is_numeric($res->response) && $res->response >= 200 && $res->response < 300) {
                            return (int) $res->response;
                        }
                    }
                }

                if ($attribute instanceof OA\Post) {
                    return 201;
                }
            }
        }

        return $default ?? 200;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::VIEW => 'onKernelView'];
    }
}
