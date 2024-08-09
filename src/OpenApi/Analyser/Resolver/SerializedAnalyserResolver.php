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
use OpenSolid\OpenApiBundle\OpenApi\Analyser\SerializedAnalyser;

readonly class SerializedAnalyserResolver implements AnalyserResolverInterface
{
    public function __construct(private SerializedAnalyser $serializedAnalyser)
    {
    }

    public function resolve(string $filename, string $format): ?AnalyserInterface
    {
        return in_array($format, ['json', 'yaml'], true) ? $this->serializedAnalyser : null;
    }
}
