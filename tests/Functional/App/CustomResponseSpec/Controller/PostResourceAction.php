<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\CustomResponseSpec\Controller;

use OpenSolid\OpenApiBundle\Attribute\Payload;
use OpenSolid\OpenApiBundle\Routing\Attribute\Post;

class PostResourceAction
{
    #[Post('/resources')]
    public function __invoke(#[Payload] PostResourcePayload $payload): void
    {
    }
}
