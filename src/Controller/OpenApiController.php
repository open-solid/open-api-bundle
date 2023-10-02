<?php

namespace Yceruto\OpenApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Yceruto\OpenApiBundle\Generator;

readonly class OpenApiController
{
    public function __construct(private Generator $generator)
    {
    }

    public function __invoke(): Response
    {
        return new Response(file_get_contents(\dirname(__DIR__, 2).'/templates/doc.html'));
    }

    public function json(): JsonResponse
    {
        $openapi = $this->generator->generate();

        return new JsonResponse($openapi->toJson(), json: true);
    }
}
