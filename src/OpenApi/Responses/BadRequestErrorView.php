<?php

namespace Yceruto\OpenApiBundle\OpenApi\Responses;

use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;
use Yceruto\OpenApiBundle\Attributes\Property;

#[Response(
    response: 400,
    description: 'Bad request',
    content: new JsonContent(type: self::class),
)]
#[Schema(readOnly: true)]
class BadRequestErrorView
{
    #[Property]
    public string $type = 'https://tools.ietf.org/html/rfc2616#section-10';

    #[Property]
    public string $title = 'Bad Request';

    #[Property]
    public int $status = 400;

    #[Property]
    public string $detail;
}
