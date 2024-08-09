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

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\PatchResourceAction\Controller;

use OpenApi\Attributes\Schema;
use OpenSolid\OpenApiBundle\Attribute\Property;

#[Schema(writeOnly: true)]
class PatchResourcePayload
{
    #[Property]
    public ?string $name = null;
}
