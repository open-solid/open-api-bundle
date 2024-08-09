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

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\GetResourceCustomAttributesAction\Model;

use OpenApi\Attributes\Schema;
use OpenSolid\OpenApiBundle\Attribute\Property;
use OpenSolid\Tests\OpenApiBundle\Functional\App\GetResourceCustomAttributesAction\Attribute\IdProperty;

#[Schema]
readonly class ResourceView
{
    #[IdProperty]
    public string $id;

    #[Property]
    public string $name;

    public static function from(string $id, string $name): self
    {
        $self = new self();
        $self->id = $id;
        $self->name = $name;

        return $self;
    }
}
