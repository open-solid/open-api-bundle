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

use OpenSolid\OpenApiBundle\Attribute\Path;
use OpenSolid\OpenApiBundle\Attribute\PathDefaults;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class Id extends Path
{
    public static function defaults(): PathDefaults
    {
        return PathDefaults::create()
            ->format('uuid');
    }
}
