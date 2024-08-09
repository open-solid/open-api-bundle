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

namespace OpenSolid\OpenApiBundle\Attribute;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestPayloadValueResolver;
use Symfony\Component\Validator\Constraints\GroupSequence;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Payload extends MapRequestPayload
{
    public function __construct(
        array|string|null $acceptFormat = null,
        array $serializationContext = [],
        string|GroupSequence|array|null $validationGroups = null,
        string $resolver = RequestPayloadValueResolver::class,
        int $validationFailedStatusCode = Response::HTTP_UNPROCESSABLE_ENTITY,
        public ?string $itemsType = null,
    ) {
        parent::__construct($acceptFormat, $serializationContext, $validationGroups, $resolver, $validationFailedStatusCode);
    }
}
