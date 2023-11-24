<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\ImportRoutes\Controller;

use OpenSolid\OpenApiBundle\Attribute\Path;
use OpenSolid\OpenApiBundle\Routing\Attribute\Get;
use OpenSolid\Tests\OpenApiBundle\Functional\App\ImportRoutes\Model\ResourceView;

class GetResourceAction
{
    #[Get(path: '/resources/{id}')]
    public function __invoke(#[Path(format: 'uuid')] string $id): ResourceView
    {
        return ResourceView::from('4f09d694-446a-4769-9929-dad96a071cad', 'foo');
    }
}
