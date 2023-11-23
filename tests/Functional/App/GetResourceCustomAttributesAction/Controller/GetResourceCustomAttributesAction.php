<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\GetResourceCustomAttributesAction\Controller;

use OpenSolid\OpenApiBundle\Routing\Attribute\Get;
use OpenSolid\Tests\OpenApiBundle\Functional\App\GetResourceCustomAttributesAction\Attribute\Id;
use OpenSolid\Tests\OpenApiBundle\Functional\App\GetResourceCustomAttributesAction\Model\ResourceView;

class GetResourceCustomAttributesAction
{
    #[Get(path: '/resources/{id}')]
    public function __invoke(#[Id] string $id): ResourceView
    {
        return ResourceView::from('4f09d694-446a-4769-9929-dad96a071cad', 'foo');
    }
}
