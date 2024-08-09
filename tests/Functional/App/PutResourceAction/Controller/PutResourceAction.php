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

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\PutResourceAction\Controller;

use OpenSolid\OpenApiBundle\Attribute\Payload;
use OpenSolid\OpenApiBundle\Routing\Attribute\Put;
use OpenSolid\Tests\OpenApiBundle\Functional\App\PutResourceAction\Model\ResourceView;

class PutResourceAction
{
    #[Put('/resources')]
    public function __invoke(#[Payload] PutResourcePayload $payload): ResourceView
    {
        return ResourceView::from('4f09d694-446a-4769-9929-dad96a071cad', $payload->name);
    }
}
