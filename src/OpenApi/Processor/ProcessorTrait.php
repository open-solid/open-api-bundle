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
use OpenApi\Annotations\Operation;
use OpenApi\Annotations\PathItem;
use OpenApi\Undefined;

trait ProcessorTrait
{
    protected function detachAnnotationRecursively(object|array $annotation, Analysis $analysis): void
    {
        if ($annotation instanceof AbstractAnnotation) {
            $analysis->annotations->offsetUnset($annotation);
        }

        foreach ($annotation as $field) {
            if (is_array($field) || $field instanceof AbstractAnnotation) {
                $this->detachAnnotationRecursively($field, $analysis);
            }
        }
    }

    /**
     * Collects every "$ref" reachable from the given annotation.
     *
     * @param \SplObjectStorage<AbstractAnnotation, null>|null $visited
     *
     * @return array<string, true>
     */
    protected function collectRefs(AbstractAnnotation $annotation, ?\SplObjectStorage $visited = null): array
    {
        $visited ??= new \SplObjectStorage();

        if ($visited->offsetExists($annotation)) {
            return [];
        }
        $visited->offsetSet($annotation);

        $refs = [];
        if (property_exists($annotation, 'ref') && \is_string($annotation->ref) && !Undefined::isDefault($annotation->ref)) {
            $refs[$annotation->ref] = true;
        }

        foreach (get_object_vars($annotation) as $property => $value) {
            if (\in_array($property, $annotation::$_blacklist, true)) {
                continue;
            }

            foreach (\is_array($value) ? $value : [$value] as $item) {
                if ($item instanceof AbstractAnnotation) {
                    $refs += $this->collectRefs($item, $visited);
                }
            }
        }

        return $refs;
    }

    /**
     * @return array<string, Operation> the defined operations of the path, indexed by method
     */
    protected function operationsOf(PathItem $pathItem): array
    {
        $operations = [];
        foreach (['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'] as $method) {
            if ($pathItem->{$method} instanceof Operation) {
                $operations[$method] = $pathItem->{$method};
            }
        }

        return $operations;
    }
}
