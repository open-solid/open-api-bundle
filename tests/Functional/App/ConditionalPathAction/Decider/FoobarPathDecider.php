<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\ConditionalPathAction\Decider;

use Symfony\Bundle\FrameworkBundle\Routing\Attribute\AsRoutingConditionService;

#[AsRoutingConditionService('foobar')]
class FoobarPathDecider
{
    public function isEnabled(): bool
    {
        return false;
    }
}
