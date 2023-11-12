<?php

namespace Yceruto\OpenApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yceruto\OpenApiBundle\Generator;

readonly class OpenApiController
{
    public function __construct(private Generator $generator)
    {
    }

    public function __invoke(): Response
    {
        return new Response(file_get_contents(\dirname(__DIR__, 2).'/templates/openapi_ui.html'));
    }

    public function json(): JsonResponse
    {
        $openapi = $this->generator->generate();

        return new JsonResponse($openapi->toJson(), json: true);
    }

    public function jsonSchema(Request $request, string $name): JsonResponse
    {
        $openapi = json_decode($this->generator->generate()->toJson(JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        $schema = $openapi['components']['schemas'][$name] ?? throw new NotFoundHttpException(sprintf('Schema "%s" not found.', $name));

        $data = [
            '$schema' => 'https://json-schema.org/draft/2020-12/schema',
            '$id' => $request->getUri(),
        ] + $schema;

        return new JsonResponse($data);
    }
}
