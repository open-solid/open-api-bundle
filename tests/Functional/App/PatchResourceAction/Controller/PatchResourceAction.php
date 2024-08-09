<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\PatchResourceAction\Controller;

use OpenSolid\OpenApiBundle\Attribute\Payload;
use OpenSolid\OpenApiBundle\Routing\Attribute\Patch;
use OpenSolid\Tests\OpenApiBundle\Functional\App\PatchResourceAction\Model\ResourceView;

class PatchResourceAction
{
    #[Patch('/resources')]
    public function __invoke(#[Payload] PatchResourcePayload $payload): ResourceView
    {
        return ResourceView::from('4f09d694-446a-4769-9929-dad96a071cad', $payload->name);
    }
}
