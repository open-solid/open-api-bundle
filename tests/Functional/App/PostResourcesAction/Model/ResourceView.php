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

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\PostResourcesAction\Model;

use OpenApi\Attributes\Schema;
use OpenSolid\OpenApiBundle\Attribute\Property;

#[Schema]
readonly class ResourceView
{
    #[Property(format: 'uuid')]
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
