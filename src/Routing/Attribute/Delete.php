<?php

namespace Yceruto\OpenApiBundle\Routing\Attribute;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Delete extends \OpenApi\Attributes\Delete
{
    use ApiRouteTrait;

    public function getMethod(): string
    {
        return 'DELETE';
    }
}
