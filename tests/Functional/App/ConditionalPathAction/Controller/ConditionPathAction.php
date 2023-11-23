<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\ConditionalPathAction\Controller;

use OpenSolid\OpenApiBundle\Routing\Attribute\Get;

class ConditionPathAction
{
    #[Get(
        path: '/conditional',
        when: 'service("foobar").isEnabled()',
    )]
    public function __invoke(): void
    {
    }
}
