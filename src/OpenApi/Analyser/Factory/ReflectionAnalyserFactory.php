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

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Factory;

use OpenApi\Analysers\AnnotationFactoryInterface;
use OpenApi\Analysers\ReflectionAnalyser;

readonly class ReflectionAnalyserFactory
{
    /**
     * @param \Traversable<AnnotationFactoryInterface> $factories
     */
    public function __invoke(\Traversable $factories): ReflectionAnalyser
    {
        return new ReflectionAnalyser(iterator_to_array($factories));
    }
}
