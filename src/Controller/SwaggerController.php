<?php

namespace Yceruto\OpenApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SwaggerController
{
    public function __construct()
    {
    }

    public function __invoke(): Response
    {
        return new Response(file_get_contents(\dirname(__DIR__, 2).'/templates/swagger.html'));
    }

    public function json(): JsonResponse
    {
        return new JsonResponse([]);
    }
}
