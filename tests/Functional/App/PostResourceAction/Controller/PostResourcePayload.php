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

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\PostResourceAction\Controller;

use OpenApi\Attributes\Schema;
use OpenSolid\OpenApiBundle\Attribute\Property;
use OpenSolid\Tests\OpenApiBundle\Functional\App\PostResourceAction\Model\ResourceStatus;

#[Schema(writeOnly: true)]
class PostResourcePayload
{
    #[Property(minLength: 3)]
    public string $name;

    #[Property(enum: ResourceStatus::class)]
    public string $status;
}
