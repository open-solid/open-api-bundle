<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\GetResourcesAction\Controller;

use OpenSolid\OpenApiBundle\Attribute\Param;

class GetResourcesParams
{
    #[Param]
    public ?string $sort = null;

    public ?GetResourcesPage $page = null;

    public ?GetResourcesFilter $filter = null;
}
