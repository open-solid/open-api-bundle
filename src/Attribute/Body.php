<?php

namespace OpenSolid\OpenApiBundle\Attribute;

use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Body extends MapRequestPayload
{
}
