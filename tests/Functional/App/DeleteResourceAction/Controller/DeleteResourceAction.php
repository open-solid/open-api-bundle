<?php

namespace Yceruto\Tests\OpenApiBundle\Functional\App\DeleteResourceAction\Controller;

use Yceruto\OpenApiBundle\Attribute\Path;
use Yceruto\OpenApiBundle\Routing\Attribute\Delete;

class DeleteResourceAction
{
    #[Delete('/resources/{id}')]
    public function __invoke(#[Path(format: 'uuid')] string $id): void
    {
    }
}
