<?php

namespace Yceruto\OpenApiBundle\Attribute;

use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Body extends MapRequestPayload
{
}
