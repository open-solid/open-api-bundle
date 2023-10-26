<?php

namespace Yceruto\OpenApiBundle\OpenApi\Responses;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;
use Yceruto\OpenApiBundle\Attribute\Property;

#[Response(
    response: 422,
    description: 'Validation error',
    content: new JsonContent(type: self::class),
)]
#[Schema(readOnly: true)]
class ValidationErrorView
{
    #[Property]
    public string $type = 'https://symfony.com/errors/validation';

    #[Property]
    public string $title = 'Validation Failed';

    #[Property]
    public int $status = 422;

    #[Property]
    public string $detail;

    #[Property(type: 'array', items: new Items(type: ValidationViolationErrorView::class))]
    public array $violations;
}
