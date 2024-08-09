<?php

declare(strict_types=1);

/*
 * This file is part of OpenSolid package.
 *
 * (c) Yonel Ceruto <open@yceruto.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenSolid\OpenApiBundle\Routing\Attribute;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Post extends \OpenApi\Attributes\Post
{
    use ApiRouteTrait;

    public function getMethod(): string
    {
        return 'POST';
    }
}
