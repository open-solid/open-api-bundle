<?php

namespace Yceruto\Tests\OpenApiBundle\Functional\App\PutResourceAction\Controller;

use OpenApi\Attributes\Schema;
use Yceruto\OpenApiBundle\Attribute\Property;

#[Schema(writeOnly: true)]
class PutResourceBody
{
    #[Property]
    public string $name;
}
