<?php

namespace Yceruto\OpenApiBundle\Tests\Functional\App\PatchResourceAction\Controller;

use Yceruto\OpenApiBundle\Attribute\Body;
use Yceruto\OpenApiBundle\Routing\Attribute\Patch;
use Yceruto\OpenApiBundle\Tests\Functional\App\PatchResourceAction\Model\ResourceView;

class PatchResourceAction
{
    #[Patch('/resources')]
    public function __invoke(#[Body] PatchResourceBody $body): ResourceView
    {
        return ResourceView::from('4f09d694-446a-4769-9929-dad96a071cad', $body->name);
    }
}
