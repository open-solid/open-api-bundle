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

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\Schema;

use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Attributes\Schema;
use OpenApi\Context;
use OpenApi\Generator;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\AnalyserGuesserInterface;

class SchemaDefaultGuesser implements AnalyserGuesserInterface
{
    public function guess(\Reflector $reflector, AbstractAnnotation $annotation, Context $context): void
    {
        if (!$reflector instanceof \ReflectionClass || !$annotation instanceof Schema) {
            return;
        }

        if (Generator::isDefault($annotation->readOnly) && $reflector->isReadOnly()) {
            $annotation->readOnly = true;
        }
    }
}
