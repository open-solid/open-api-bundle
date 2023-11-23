<?php

namespace OpenSolid\OpenApiBundle\HttpKernel\Controller;

use OpenApi\Annotations\Operation;
use OpenApi\Attributes as OA;
use OpenApi\Generator;
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

        if (!$result) {
            $event->setResponse(new Response(status: 204));
            return;
        }

        if ($result instanceof Response || null === $event->controllerArgumentsEvent) {
            return;
        }

        $request = $event->getRequest();
        $content = $this->serializer->serialize($result, $request->getPreferredFormat('json'));
        $statusCode = $this->guessStatusCode($event->controllerArgumentsEvent->getAttributes());
        $contentType = $request->getAcceptableContentTypes()[0] ?? $request->headers->get('CONTENT_TYPE', 'application/json');

        $event->setResponse(new Response($content, $statusCode, ['Content-Type' => $contentType]));
    }

    protected function guessStatusCode(array $controllerAttributes): int
    {
        foreach ($controllerAttributes as $attributes) {
            foreach ($attributes as $attribute) {
                if (!$attribute instanceof Operation) {
                    continue;
                }

                if (!Generator::isDefault($attribute->responses)) {
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

        return 200;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::VIEW => 'onKernelView'];
    }
}
