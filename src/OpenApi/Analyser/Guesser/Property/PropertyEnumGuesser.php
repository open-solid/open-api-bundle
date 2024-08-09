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

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\Property;

use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Attributes\Property;
use OpenApi\Context;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\AnalyserGuesserInterface;

class PropertyEnumGuesser implements AnalyserGuesserInterface
{
    public function guess(\Reflector $reflector, AbstractAnnotation $annotation, Context $context): void
    {
        if (!$reflector instanceof \ReflectionProperty || !$annotation instanceof Property) {
            return;
        }

        if (null === $type = $reflector->getType()) {
            return;
        }

        if ($type instanceof \ReflectionNamedType && !$type->isBuiltin() && is_subclass_of($type->getName(), \BackedEnum::class)) {
            $enumType = new \ReflectionEnum($type->getName());
            $annotation->enum = array_map(static fn (\ReflectionEnumBackedCase $case) => $case->getValue(), $enumType->getCases());
            $annotation->type = $enumType->getBackingType()?->getName();
        }
    }
}
