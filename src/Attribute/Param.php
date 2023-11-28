<?php

namespace OpenSolid\OpenApiBundle\Attribute;

use OpenApi\Annotations\Parameter;
use OpenApi\Attributes\ParameterTrait;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class Param extends Parameter
{
    use ParameterTrait;

    public $in = 'query';
}
