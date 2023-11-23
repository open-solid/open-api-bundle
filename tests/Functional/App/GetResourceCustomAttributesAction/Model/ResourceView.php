<?php

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
