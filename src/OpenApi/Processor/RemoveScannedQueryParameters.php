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

namespace OpenSolid\OpenApiBundle\OpenApi\Processor;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;

/**
 * Removes the query parameters the scanner picks up from the properties of a query DTO.
 *
 * They are not reusable components: the operation guessers create them again from the
 * #[MapQueryString] argument and attach them to the operation. Without this removal,
 * MergeIntoComponents moves them into "components.parameters".
 */
readonly class RemoveScannedQueryParameters implements ProcessorInterface
{
    public function __invoke(Analysis $analysis): void
    {
        foreach ($analysis->getAnnotationsOfType(OA\Parameter::class) as $annotation) {
            if ('query' !== $annotation->in || $annotation->_context->nested) {
                continue;
            }

            if (($annotation->_context->reflector ?? null) instanceof \ReflectionProperty) {
                $analysis->annotations->offsetUnset($annotation);
            }
        }
    }
}
