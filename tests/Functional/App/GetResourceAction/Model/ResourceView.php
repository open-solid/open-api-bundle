<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\GetResourceAction\Model;

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
