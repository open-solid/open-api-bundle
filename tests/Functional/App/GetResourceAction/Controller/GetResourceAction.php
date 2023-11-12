<?php

namespace Yceruto\OpenApiBundle\Tests\Functional\App\GetResourceAction\Controller;

use Yceruto\OpenApiBundle\Attribute\Path;
use Yceruto\OpenApiBundle\Routing\Attribute\Get;
use Yceruto\OpenApiBundle\Tests\Functional\App\GetResourceAction\Model\ResourceView;

class GetResourceAction
{
    #[Get(path: '/resources/{id}')]
    public function __invoke(#[Path(format: 'uuid')] string $id): ResourceView
    {
        return ResourceView::from('4f09d694-446a-4769-9929-dad96a071cad', 'foo');
    }
}
