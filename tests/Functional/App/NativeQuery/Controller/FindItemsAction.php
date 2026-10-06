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

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\NativeQuery\Controller;

use OpenApi\Attributes as OA;
use OpenSolid\OpenApiBundle\Attribute\Path;
use OpenSolid\OpenApiBundle\Routing\Attribute\Get;
use OpenSolid\Tests\OpenApiBundle\Functional\App\NativeQuery\Model\ItemFilter;
use OpenSolid\Tests\OpenApiBundle\Functional\App\NativeQuery\Model\ItemView;
use OpenSolid\Tests\OpenApiBundle\Functional\App\NativeQuery\Model\Status;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;

class FindItemsAction
{
    /**
     * @param int         $page   The page number
     * @param Status|null $status Filter by status
     *
     * @return list<ItemView>
     */
    #[Get('/stores/{store}/items')]
    public function __invoke(
        #[Path] string $store,
        #[MapQueryString(key: 'filter')] ?ItemFilter $filter = null,
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter] ?Status $status = null,
        #[MapQueryParameter(name: 'q')] ?string $search = null,
        #[MapQueryParameter, OA\QueryParameter(description: 'Explicit description')] ?string $sort = null,
        #[MapQueryParameter(name: 'filter[name]')] ?string $duplicated = null,
    ): array {
        if ('empty' === $search) {
            return [];
        }

        return [new ItemView($store, $filter?->name ?? 'none', $page, $status)];
    }
}
