<?php

namespace Yceruto\OpenApiBundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class OpenApiBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
