<?php

declare(strict_types=1);

/*
 * This file is part of OpenSolid package.
 *
 * (c) Yonel Ceruto <open@yceruto.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\GetResourceCustomAttributesAction\Attribute;

use OpenSolid\OpenApiBundle\Attribute\Property;
use OpenSolid\OpenApiBundle\Attribute\PropertyDefaults;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::TARGET_CLASS_CONSTANT | \Attribute::IS_REPEATABLE)]
class IdProperty extends Property
{
    public static function defaults(): PropertyDefaults
    {
        return PropertyDefaults::create()
            ->format('uuid');
    }
}
