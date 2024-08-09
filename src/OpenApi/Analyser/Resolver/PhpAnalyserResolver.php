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

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Resolver;

use OpenApi\Analysers\AnalyserInterface;
use OpenApi\Analysers\ReflectionAnalyser;

readonly class PhpAnalyserResolver implements AnalyserResolverInterface
{
    public function __construct(private ReflectionAnalyser $reflectionAnalyser)
    {
    }

    public function resolve(string $filename, string $format): ?AnalyserInterface
    {
        return 'php' === $format ? $this->reflectionAnalyser : null;
    }
}
