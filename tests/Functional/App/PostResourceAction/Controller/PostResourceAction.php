<?php

namespace Yceruto\OpenApiBundle\Tests\Functional\App\PostResourceAction\Controller;

use Yceruto\OpenApiBundle\Attribute\Body;
use Yceruto\OpenApiBundle\Routing\Attribute\Post;
use Yceruto\OpenApiBundle\Tests\Functional\App\PostResourceAction\Model\ResourceView;

class PostResourceAction
{
    #[Post('/resources')]
    public function __invoke(#[Body] PostResourceBody $body): ResourceView
    {
        return ResourceView::from('4f09d694-446a-4769-9929-dad96a071cad', $body->name);
    }
}
