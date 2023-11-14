<?php

namespace Yceruto\Tests\OpenApiBundle\Functional\App\GetResourceCustomPathAttributeAction\Attribute;

use Yceruto\OpenApiBundle\Attribute\Path;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class Id extends Path
{
    public $_format = 'uuid';
}
