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

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser;

use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Context;

interface AnalyserGuesserInterface
{
    public function guess(\Reflector $reflector, AbstractAnnotation $annotation, Context $context): void;
}
