<?php

namespace OpenSolid\OpenApiBundle\Routing\Attribute;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Get extends \OpenApi\Attributes\Get
{
    use ApiRouteTrait;

    public function getMethod(): string
    {
        return 'GET';
    }
}
