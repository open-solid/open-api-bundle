<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\CustomResponseSpec\Controller;

use OpenSolid\OpenApiBundle\Attribute\Body;
use OpenSolid\OpenApiBundle\Routing\Attribute\Post;

class PostResourceAction
{
    #[Post('/resources')]
    public function __invoke(#[Body] PostResourceBody $body): void
    {
    }
}
