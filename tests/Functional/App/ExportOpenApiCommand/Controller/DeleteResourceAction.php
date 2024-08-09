<?php

declare(strict_types=1);

/*
 * This file is part of OpenSolid package.
 *
 * (c) Yonel Ceruto <open@yceruto.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
