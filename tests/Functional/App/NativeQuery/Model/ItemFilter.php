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
use Symfony\Component\Validator\Constraints as Assert;

class ItemFilter
{
    /**
     * Filter by name.
     */
    #[Assert\Length(max: 20)]
    #[OA\QueryParameter]
    public ?string $name = null;

    /**
     * @var list<string> Filter by tags
     */
    #[OA\QueryParameter]
    public array $tags = [];

    #[OA\QueryParameter]
    public ?Kind $kind = null;

    #[OA\QueryParameter(schema: new OA\Schema(enum: Status::class))]
    public ?string $status = null;

    #[Assert\NotBlank]
    #[OA\QueryParameter(required: true)]
    public string $currency = 'USD';

    #[OA\QueryParameter]
    public ?\DateTimeImmutable $since = null;

    #[OA\QueryParameter(name: 'min_price')]
    public ?float $minPrice = null;

    public ?int $ignored = null;
}
