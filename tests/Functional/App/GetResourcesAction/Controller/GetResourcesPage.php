<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\GetResourcesAction\Controller;

use OpenSolid\OpenApiBundle\Attribute\Param;

class GetResourcesPage
{
    #[Param]
    public ?int $offset = 0;

    #[Param]
    public ?int $limit = 10;
}
