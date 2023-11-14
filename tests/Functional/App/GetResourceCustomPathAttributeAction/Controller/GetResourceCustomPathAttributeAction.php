<?php

namespace Yceruto\Tests\OpenApiBundle\Functional\App\GetResourceCustomPathAttributeAction\Controller;

use Yceruto\OpenApiBundle\Routing\Attribute\Get;
use Yceruto\Tests\OpenApiBundle\Functional\App\GetResourceCustomPathAttributeAction\Attribute\Id;
use Yceruto\Tests\OpenApiBundle\Functional\App\GetResourceCustomPathAttributeAction\Model\ResourceView;

class GetResourceCustomPathAttributeAction
{
    #[Get(path: '/resources/{id}')]
    public function __invoke(#[Id] string $id): ResourceView
    {
        return ResourceView::from('4f09d694-446a-4769-9929-dad96a071cad', 'foo');
    }
}
