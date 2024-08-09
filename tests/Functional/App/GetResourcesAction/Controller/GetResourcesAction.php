<?php

declare(strict_types=1);

/*
 * This file is part of OpenSolid package.
 *
 * (c) Yonel Ceruto <open@yceruto.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\GetResourcesAction\Controller;

use OpenSolid\OpenApiBundle\Attribute\Query;
use OpenSolid\OpenApiBundle\Routing\Attribute\Get;
use OpenSolid\Tests\OpenApiBundle\Functional\App\GetResourcesAction\Model\ResourceView;

class GetResourcesAction
{
    #[Get(
        path: '/resources',
        itemsType: ResourceView::class,
    )]
    public function __invoke(#[Query] ?GetResourcesParams $params = null): array
    {
        return [
            ResourceView::from('4f09d694-446a-4769-9929-dad96a071cad', 'foo'),
            ResourceView::from('e5dda7fb-4c87-4b62-9cee-5d46d9180521', 'bar'),
        ];
    }
}
