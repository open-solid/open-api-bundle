<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\PatchResourceAction\Controller;

use OpenApi\Attributes\Schema;
use OpenSolid\OpenApiBundle\Attribute\Property;

#[Schema(writeOnly: true)]
class PatchResourceBody
{
    #[Property]
    public ?string $name = null;
}
