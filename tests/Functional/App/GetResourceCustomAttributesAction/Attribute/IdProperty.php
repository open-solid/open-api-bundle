<?php

namespace Yceruto\Tests\OpenApiBundle\Functional\App\GetResourceCustomAttributesAction\Attribute;

use Yceruto\OpenApiBundle\Attribute\Property;
use Yceruto\OpenApiBundle\Attribute\PropertyDefaults;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::TARGET_CLASS_CONSTANT | \Attribute::IS_REPEATABLE)]
class IdProperty extends Property
{
    public static function defaults(): PropertyDefaults
    {
        return PropertyDefaults::create()
            ->format('uuid');
    }
}
