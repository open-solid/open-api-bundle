<?php

namespace Yceruto\OpenApiBundle\Tests\Functional\App\GetResourcesAction\Controller;

use Yceruto\OpenApiBundle\Routing\Attribute\Get;
use Yceruto\OpenApiBundle\Tests\Functional\App\GetResourcesAction\Model\ResourceView;

class GetResourceAction
{
    #[Get(
        path: '/resources',
        itemsType: ResourceView::class,
    )]
    public function __invoke(): array
    {
        return [
            ResourceView::from('4f09d694-446a-4769-9929-dad96a071cad', 'foo'),
            ResourceView::from('e5dda7fb-4c87-4b62-9cee-5d46d9180521', 'bar'),
        ];
    }
}
