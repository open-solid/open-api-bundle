<?php

namespace OpenSolid\OpenApiBundle\Attribute;

use Symfony\Component\HttpKernel\Attribute\MapQueryString;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Filter extends MapQueryString
{
}
