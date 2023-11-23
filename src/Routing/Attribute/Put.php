<?php

namespace OpenSolid\OpenApiBundle\Routing\Attribute;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Put extends \OpenApi\Attributes\Put
{
    use ApiRouteTrait;

    public function getMethod(): string
    {
        return 'PUT';
    }
}
