<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\PutResourceAction\Controller;

use OpenSolid\OpenApiBundle\Attribute\Body;
use OpenSolid\OpenApiBundle\Routing\Attribute\Put;
use OpenSolid\Tests\OpenApiBundle\Functional\App\PutResourceAction\Model\ResourceView;

class PutResourceAction
{
    #[Put('/resources')]
    public function __invoke(#[Body] PutResourceBody $body): ResourceView
    {
        return ResourceView::from('4f09d694-446a-4769-9929-dad96a071cad', $body->name);
    }
}
