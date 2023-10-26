<?php

namespace Yceruto\OpenApiBundle\Attribute;

use Symfony\Component\HttpKernel\Attribute\MapQueryString;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Query extends MapQueryString
{
}
