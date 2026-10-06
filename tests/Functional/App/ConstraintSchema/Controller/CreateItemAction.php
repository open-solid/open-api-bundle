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

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\ConstraintSchema\Controller;

use OpenSolid\OpenApiBundle\Routing\Attribute\Post;
use OpenSolid\Tests\OpenApiBundle\Functional\App\ConstraintSchema\Model\CreateItemPayload;
use OpenSolid\Tests\OpenApiBundle\Functional\App\ConstraintSchema\Model\ItemView;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class CreateItemAction
{
    #[Post('/items')]
    public function __invoke(#[MapRequestPayload] CreateItemPayload $payload): ItemView
    {
        return new ItemView($payload->name, $payload->size);
    }
}
