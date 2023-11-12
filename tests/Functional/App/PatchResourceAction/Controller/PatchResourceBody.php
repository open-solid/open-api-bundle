<?php

namespace Yceruto\OpenApiBundle\Tests\Functional\App\PatchResourceAction\Controller;

use OpenApi\Attributes\Schema;
use Yceruto\OpenApiBundle\Attribute\Property;

#[Schema(writeOnly: true)]
class PatchResourceBody
{
    #[Property]
    public ?string $name = null;
}
