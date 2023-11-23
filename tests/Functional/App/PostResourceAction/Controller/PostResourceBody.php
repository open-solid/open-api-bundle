<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\PostResourceAction\Controller;

use OpenApi\Attributes\Schema;
use OpenSolid\OpenApiBundle\Attribute\Property;

#[Schema(writeOnly: true)]
class PostResourceBody
{
    #[Property(minLength: 3)]
    public string $name;
}
