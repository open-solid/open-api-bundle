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

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\NativeQuery\Model;

use OpenApi\Attributes as OA;

#[OA\Schema]
readonly class ItemView
{
    public function __construct(
        #[OA\Property]
        public string $store,
        #[OA\Property]
        public string $name,
        #[OA\Property]
        public int $page,
        #[OA\Property]
        public ?Status $status = null,
    ) {
    }
}
