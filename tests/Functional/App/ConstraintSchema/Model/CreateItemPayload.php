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

namespace OpenSolid\Tests\OpenApiBundle\Functional\App\ConstraintSchema\Model;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema]
class CreateItemPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 50)]
        #[OA\Property]
        public string $name,
        #[OA\Property]
        public Size $size,
        #[OA\Property]
        public string $currency = 'USD',
        #[OA\Property]
        public ?Color $color = null,
    ) {
    }

    #[Assert\NotBlank]
    #[OA\Property]
    public ?string $code = null;

    #[Assert\NotNull]
    #[OA\Property]
    public ?int $quantity = null;

    #[Assert\NotBlank(allowNull: true)]
    #[OA\Property]
    public ?string $optionalNotBlank = null;

    #[Assert\NotBlank]
    #[OA\Property]
    public ?int $notBlankInteger = null;

    #[Assert\Email]
    #[OA\Property]
    public ?string $email = null;

    #[Assert\Email]
    #[OA\Property(format: 'idn-email')]
    public ?string $explicitFormat = null;

    #[Assert\Uuid]
    #[OA\Property]
    public ?string $uuid = null;

    #[Assert\Ulid]
    #[OA\Property]
    public ?string $ulid = null;

    #[Assert\Url(requireTld: true)]
    #[OA\Property]
    public ?string $url = null;

    #[Assert\Hostname]
    #[OA\Property]
    public ?string $hostname = null;

    #[Assert\Ip]
    #[OA\Property]
    public ?string $ipv4 = null;

    #[Assert\Ip(version: Assert\Ip::V6)]
    #[OA\Property]
    public ?string $ipv6 = null;

    #[Assert\Date]
    #[OA\Property]
    public ?string $date = null;

    #[Assert\DateTime]
    #[OA\Property]
    public ?string $dateTime = null;

    #[Assert\Time]
    #[OA\Property]
    public ?string $time = null;

    #[Assert\Range(min: 1, max: 10)]
    #[OA\Property]
    public ?int $range = null;

    #[Assert\Positive]
    #[OA\Property]
    public ?int $positive = null;

    #[Assert\PositiveOrZero]
    #[OA\Property]
    public ?int $positiveOrZero = null;

    #[Assert\Negative]
    #[OA\Property]
    public ?int $negative = null;

    #[Assert\NegativeOrZero]
    #[OA\Property]
    public ?int $negativeOrZero = null;

    #[Assert\GreaterThan('today')]
    #[OA\Property]
    public ?string $notNumericComparison = null;

    #[Assert\Regex('/^[a-z]+$/')]
    #[OA\Property]
    public ?string $slug = null;

    #[Assert\Regex(pattern: '/^[0-9]+$/', match: false)]
    #[OA\Property]
    public ?string $notDigits = null;

    #[Assert\Choice(choices: ['draft', 'published'])]
    #[OA\Property]
    public ?string $status = null;

    #[Assert\Choice(choices: ['a', 'b'], multiple: true)]
    #[OA\Property]
    public array $multiple = [];

    /**
     * @var list<string>
     */
    #[Assert\Count(min: 1, max: 3)]
    #[Assert\All([new Assert\NotBlank(), new Assert\Length(max: 5)])]
    #[OA\Property]
    public array $tags = [];

    #[Assert\All([new Assert\Positive()])]
    #[OA\Property]
    public array $untypedItems = [];

    /**
     * @var list<Size>
     */
    #[OA\Property]
    public array $sizes = [];

    #[OA\Property]
    public ?Size $optionalSize = null;
}
