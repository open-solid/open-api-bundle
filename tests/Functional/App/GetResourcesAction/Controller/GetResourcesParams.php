<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\GetResourcesAction\Controller;

use OpenSolid\OpenApiBundle\Attribute\Param;
use OpenSolid\Tests\OpenApiBundle\Functional\App\GetResourcesAction\Request\Query\Filter;
use OpenSolid\Tests\OpenApiBundle\Functional\App\GetResourcesAction\Request\Query\Page;

class GetResourcesParams
{
    #[Param]
    public ?string $sort = null;

    #[Param]
    public ?Page $page = null;

    #[Param]
    public ?Filter $filter = null;
}
