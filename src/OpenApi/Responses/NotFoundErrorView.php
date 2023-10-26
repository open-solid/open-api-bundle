<?php

namespace Yceruto\OpenApiBundle\OpenApi\Responses;

use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;
use Yceruto\OpenApiBundle\Attribute\Property;

#[Response(
    response: 404,
    description: 'Not found',
    content: new JsonContent(type: self::class),
)]
#[Schema(readOnly: true)]
class NotFoundErrorView
{
    #[Property]
    public string $type = 'https://tools.ietf.org/html/rfc2616#section-10';

    #[Property]
    public string $title = 'Not Found';

    #[Property]
    public int $status = 404;

    #[Property]
    public string $detail;
}
