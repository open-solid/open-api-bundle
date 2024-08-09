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

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\GetResourcesAction\Controller;

use OpenSolid\OpenApiBundle\Attribute\Param;

class GetResourcesFilter
{
    #[Param]
    public ?string $name = null;
}
