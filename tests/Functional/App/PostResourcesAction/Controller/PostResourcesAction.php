<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\PostResourcesAction\Controller;

use OpenSolid\OpenApiBundle\Attribute\Body;
use OpenSolid\OpenApiBundle\Routing\Attribute\Post;
use OpenSolid\Tests\OpenApiBundle\Functional\App\PostResourcesAction\Model\ResourceView;

class PostResourcesAction
{
    #[Post('/resources')]
    public function __invoke(#[Body(itemsType: PostResourceBody::class)] array $body): ResourceView
    {
        return ResourceView::from('4f09d694-446a-4769-9929-dad96a071cad', $body[0]->name);
    }
}
