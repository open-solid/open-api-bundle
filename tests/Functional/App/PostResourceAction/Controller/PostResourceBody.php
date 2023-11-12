<?php

namespace Yceruto\OpenApiBundle\Tests\Functional\App\PostResourceAction\Controller;

use OpenApi\Attributes\Schema;
use Yceruto\OpenApiBundle\Attribute\Property;

#[Schema(writeOnly: true)]
class PostResourceBody
{
    #[Property(minLength: 3)]
    public string $name;
}
