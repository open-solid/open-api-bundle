<?php

namespace Yceruto\OpenApiBundle\Tests\Functional\App\PutResourceAction\Controller;

use Yceruto\OpenApiBundle\Attribute\Body;
use Yceruto\OpenApiBundle\Routing\Attribute\Put;
use Yceruto\OpenApiBundle\Tests\Functional\App\PostResourceAction\Model\ResourceView;

class PutResourceAction
{
    #[Put('/resources')]
    public function __invoke(#[Body] PutResourceBody $body): ResourceView
    {
        return ResourceView::from('4f09d694-446a-4769-9929-dad96a071cad', $body->name);
    }
}
