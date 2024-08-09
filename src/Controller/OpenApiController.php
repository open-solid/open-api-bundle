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

namespace OpenSolid\OpenApiBundle\Controller;

use OpenSolid\OpenApiBundle\Generator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class OpenApiController
{
    private string $template;

    public function __construct(
        private Generator $generator,
        ?string $template = null,
    ) {
        $this->template = $template ?? \dirname(__DIR__, 2).'/templates/openapi_ui.html.php';
    }

    public function index(UrlGeneratorInterface $urlGenerator): Response
    {
        if (null === $openapi = $this->generator->generate()) {
            throw new NotFoundHttpException('OpenAPI spec not found.');
        }

        $validationErrors = '';
        try {
            if (!$openapi->validate()) {
                throw new \ErrorException('OpenAPI spec is invalid.');
            }
        } catch (\ErrorException $e) {
            $validationErrors = $e->getMessage();
        }

        $content = $this->render($this->template, [
            'url' => $urlGenerator->generate('openapi_json', [], UrlGeneratorInterface::RELATIVE_PATH),
            'validation_errors' => $validationErrors,
        ]);

        return new Response($content);
    }

    public function yaml(): Response
    {
        if (null === $openapi = $this->generator->generate()) {
            throw new NotFoundHttpException('OpenAPI spec not found.');
        }

        return new Response($openapi->toYaml(), 200, [
            'Content-Type' => 'application/x-yaml',
        ]);
    }

    public function json(): JsonResponse
    {
        if (null === $openapi = $this->generator->generate()) {
            throw new NotFoundHttpException('OpenAPI spec not found.');
        }

        return new JsonResponse($openapi->toJson(), json: true);
    }

    public function jsonSchema(Request $request, string $name): JsonResponse
    {
        if (null === $openapi = $this->generator->generate()) {
            throw new NotFoundHttpException('OpenAPI spec not found.');
        }

        $openapi = json_decode($openapi->toJson(JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        $schema = $openapi['components']['schemas'][$name] ?? throw new NotFoundHttpException(sprintf('Schema "%s" not found.', $name));

        $data = [
            '$schema' => 'https://json-schema.org/draft/2020-12/schema',
            '$id' => $request->getUri(),
        ] + $schema;

        return new JsonResponse($data);
    }

    private function render(string $name, array $context = []): string
    {
        extract($context, \EXTR_SKIP);
        ob_start();

        include is_file(\dirname(__DIR__).'/Resources/'.$name) ? \dirname(__DIR__).'/Resources/'.$name : $name;

        return trim(ob_get_clean());
    }
}
