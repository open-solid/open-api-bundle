<?php

namespace Yceruto\OpenApiBundle\OpenApi\Responses;

use OpenApi\Attributes\AdditionalProperties;
use OpenApi\Attributes\Schema;
use Yceruto\OpenApiBundle\Attributes\Property;

#[Schema(readOnly: true)]
class ValidationViolationErrorView
{
    #[Property]
    public string $propertyPath;

    #[Property]
    public string $title;

    #[Property]
    public string $template;

    #[Property(type: 'object', additionalProperties: new AdditionalProperties(type: 'string'))]
    public string $parameters;

    #[Property(example: 'urn:uuid:9ff3fdc4-b214-49db-8718-39c315e33d45')]
    public string $type;
}
