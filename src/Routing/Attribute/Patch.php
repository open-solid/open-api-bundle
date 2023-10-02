<?php

namespace Yceruto\OpenApiBundle\Routing\Attribute;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Patch extends \OpenApi\Attributes\Patch
{
    use ApiRouteTrait;

    public function getMethod(): string
    {
        return 'PATCH';
    }
}
