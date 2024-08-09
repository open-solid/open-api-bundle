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

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\Operation;

use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Annotations\Operation;
use OpenApi\Attributes as OA;
use OpenApi\Context;
use OpenApi\Generator;
use OpenSolid\OpenApiBundle\Attribute\Payload;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\AnalyserGuesserInterface;

class OperationRequestBodyGuesser implements AnalyserGuesserInterface
{
    public function guess(\Reflector $reflector, AbstractAnnotation $annotation, Context $context): void
    {
        if (!$reflector instanceof \ReflectionMethod || !$annotation instanceof Operation) {
            return;
        }

        if (!Generator::isDefault($annotation->requestBody)) {
            return;
        }

        if (!$annotation instanceof OA\Post && !$annotation instanceof OA\Put && !$annotation instanceof OA\Patch) {
            return;
        }

        foreach ($reflector->getParameters() as $rp) {
            foreach ($rp->getAttributes(Payload::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                $type = (($rnt = $rp->getType()) && $rnt instanceof \ReflectionNamedType) ? $rnt->getName() : null;

                if (null === $type) {
                    continue;
                }

                $bodyAttribute = $attribute->newInstance();

                $annotation->requestBody = new OA\RequestBody(required: !$rnt->allowsNull());
                $annotation->requestBody->_context = new Context(['nested' => $annotation], $context);
                $jsonContent = new OA\JsonContent(type: $type);
                if ('array' === $type && null !== $bodyAttribute->itemsType) {
                    $jsonContent->items = new OA\Items(type: $bodyAttribute->itemsType);
                }
                $jsonContent->_context = new Context(['nested' => $annotation->requestBody], $context);
                $annotation->requestBody->merge([$jsonContent]);
            }
        }
    }
}
