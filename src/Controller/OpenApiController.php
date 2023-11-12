<?php

namespace Yceruto\OpenApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Yceruto\OpenApiBundle\Generator;

readonly class OpenApiController
{
    public function __construct(private Generator $generator)
    {
    }

    public function __invoke(UrlGeneratorInterface $urlGenerator): Response
    {
        $filename = \dirname(__DIR__, 2).'/templates/openapi_ui.html';

        if (false === $content = file_get_contents($filename)) {
            throw new \RuntimeException('Unable to read the OpenAPI UI template file.');
        }

        $openapiUrl = $urlGenerator->generate('openapi_json', [], UrlGeneratorInterface::RELATIVE_PATH);
        $content = str_replace('{{ openapi_url }}', $openapiUrl, $content);

        return new Response($content);
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
