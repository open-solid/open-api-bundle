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
use OpenApi\Annotations\AbstractAnnotation;

trait ProcessorTrait
{
    protected function detachAnnotationRecursively(object|array $annotation, Analysis $analysis): void
    {
        if ($annotation instanceof AbstractAnnotation) {
            $analysis->annotations->detach($annotation);
        }

        foreach ($annotation as $field) {
            if (is_array($field) || $field instanceof AbstractAnnotation) {
                $this->detachAnnotationRecursively($field, $analysis);
            }
        }
    }
}
