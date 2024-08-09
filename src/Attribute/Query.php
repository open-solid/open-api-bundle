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

namespace OpenSolid\OpenApiBundle\Attribute;

use Symfony\Component\HttpKernel\Attribute\MapQueryString;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Query extends MapQueryString
{
}
