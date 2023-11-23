<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\PutResourceAction\Controller;

use OpenApi\Attributes\Schema;
use OpenSolid\OpenApiBundle\Attribute\Property;

#[Schema(writeOnly: true)]
class PutResourceBody
{
    #[Property]
    public string $name;
}
