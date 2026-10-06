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

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\ResponseInference\Controller;

use OpenApi\Attributes as OA;
use OpenSolid\OpenApiBundle\Attribute\Path;
use OpenSolid\OpenApiBundle\Routing\Attribute\Delete;
use OpenSolid\OpenApiBundle\Routing\Attribute\Get;
use OpenSolid\OpenApiBundle\Routing\Attribute\Head;
use OpenSolid\OpenApiBundle\Routing\Attribute\Options;
use OpenSolid\OpenApiBundle\Routing\Attribute\Patch;
use OpenSolid\OpenApiBundle\Routing\Attribute\Post;
use OpenSolid\OpenApiBundle\Routing\Attribute\Put;
use OpenSolid\Tests\OpenApiBundle\Functional\App\ResponseInference\Model\ItemPayload;
use OpenSolid\Tests\OpenApiBundle\Functional\App\ResponseInference\Model\ItemView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[OA\SecurityScheme(securityScheme: 'bearer', type: 'http', scheme: 'bearer')]
class ItemsController
{
    #[Post(
        path: '/items',
        security: [['bearer' => []]],
        responses: [
            new OA\Response(response: 201, description: 'The created item'),
            new OA\Response(response: 409, description: 'The item already exists'),
        ],
    )]
    public function create(#[MapRequestPayload] ItemPayload $payload): ItemView
    {
        return new ItemView($payload->name);
    }

    #[Put(
        path: '/items/{id}',
        responses: [
            new OA\Response(response: 200, description: 'Custom body', content: new OA\JsonContent(type: 'object')),
        ],
    )]
    public function replace(#[Path] string $id, #[MapRequestPayload] ItemPayload $payload): ItemView
    {
        return new ItemView($payload->name);
    }

    #[Delete(path: '/items/{id}', statusCode: 202)]
    public function delete(#[Path] string $id): void
    {
    }

    #[Head(path: '/items/{id}')]
    public function exists(#[Path] string $id): ItemView
    {
        return new ItemView($id);
    }

    #[Options(path: '/items')]
    public function options(): Response
    {
        return new Response(headers: ['Allow' => 'GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS']);
    }

    /**
     * @return list<ItemView>
     */
    #[Put(path: '/items')]
    public function replaceAll(#[MapRequestPayload(type: ItemPayload::class)] array $items): array
    {
        return array_map(static fn (ItemPayload $item) => new ItemView($item->name), $items);
    }

    /**
     * @param list<ItemPayload> $items
     */
    #[Patch(path: '/items')]
    public function patchAll(#[MapRequestPayload] array $items): void
    {
    }

    /**
     * @return list<int>
     */
    #[Get(path: '/items/ids', security: [])]
    public function ids(): array
    {
        return [1, 2];
    }
}
