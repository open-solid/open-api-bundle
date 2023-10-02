<?php

namespace Yceruto\OpenApiBundle\Attributes;

use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Payload extends MapRequestPayload
{
}
