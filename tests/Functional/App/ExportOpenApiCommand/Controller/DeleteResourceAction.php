<?php

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\ExportOpenApiCommand\Controller;

use OpenSolid\OpenApiBundle\Attribute\Path;
use OpenSolid\OpenApiBundle\Routing\Attribute\Delete;

class DeleteResourceAction
{
    #[Delete('/resources/{id}')]
    public function __invoke(#[Path(format: 'uuid')] string $id): void
    {
    }
}
