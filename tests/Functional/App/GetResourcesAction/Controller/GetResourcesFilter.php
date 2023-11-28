<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\GetResourcesAction\Controller;

use OpenSolid\OpenApiBundle\Attribute\Param;

class GetResourcesFilter
{
    #[Param]
    public ?string $name = null;
}
