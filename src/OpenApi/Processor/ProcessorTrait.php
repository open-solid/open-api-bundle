<?php

namespace Yceruto\OpenApiBundle\OpenApi\Processor;

use OpenApi\Analysis;
use OpenApi\Annotations\AbstractAnnotation;

trait ProcessorTrait
{
    protected function detachAnnotationRecursively($annotation, Analysis $analysis): void
    {
        if ($annotation instanceof AbstractAnnotation) {
            $analysis->annotations->detach($annotation);
        }

        if (is_array($annotation) || is_object($annotation)) {
            foreach ($annotation as $field) {
                if (is_array($field) || $field instanceof AbstractAnnotation) {
                    $this->detachAnnotationRecursively($field, $analysis);
                }
            }
        }
    }
}
