<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\PostResourcesAction\Controller;

use OpenSolid\OpenApiBundle\Attribute\Payload;
use OpenSolid\OpenApiBundle\Routing\Attribute\Post;
use OpenSolid\Tests\OpenApiBundle\Functional\App\PostResourcesAction\Model\ResourceView;

class PostResourcesAction
{
    #[Post('/resources')]
    public function __invoke(#[Payload(itemsType: PostResourcePayload::class)] array $payload): ResourceView
    {
        return ResourceView::from('4f09d694-446a-4769-9929-dad96a071cad', $payload[0]->name);
    }
}
