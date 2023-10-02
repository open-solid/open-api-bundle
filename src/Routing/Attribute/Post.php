<?php

namespace Yceruto\OpenApiBundle\Routing\Attribute;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Post extends \OpenApi\Attributes\Post
{
    use ApiRouteTrait;

    public function getMethod(): string
    {
        return 'POST';
    }
}
