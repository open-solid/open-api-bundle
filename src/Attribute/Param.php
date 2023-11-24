<?php

namespace OpenSolid\OpenApiBundle\Attribute;

use OpenApi\Attributes\QueryParameter;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class Param extends QueryParameter
{
}
