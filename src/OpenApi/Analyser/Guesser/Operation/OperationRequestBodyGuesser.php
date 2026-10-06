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
use OpenApi\Undefined;
use OpenSolid\OpenApiBundle\Attribute\Payload;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\AnalyserGuesserInterface;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\CollectionItemsTypeGuesser;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

/**
 * Guesses the operation request body from the #[MapRequestPayload] (or #[Payload]) argument.
 */
class OperationRequestBodyGuesser implements AnalyserGuesserInterface
{
    public function guess(\Reflector $reflector, AbstractAnnotation $annotation, Context $context): void
    {
        if (!$reflector instanceof \ReflectionMethod || !$annotation instanceof Operation) {
            return;
        }

        if (!Undefined::isDefault($annotation->requestBody)) {
            return;
        }

        if (!$annotation instanceof OA\Post && !$annotation instanceof OA\Put && !$annotation instanceof OA\Patch) {
            return;
        }

        foreach ($reflector->getParameters() as $rp) {
            foreach ($rp->getAttributes(MapRequestPayload::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                if (!($rnt = $rp->getType()) instanceof \ReflectionNamedType) {
                    continue;
                }

                $type = $rnt->getName();
                $payload = $attribute->newInstance();

                $annotation->requestBody = new OA\RequestBody(required: !$rnt->allowsNull());
                $annotation->requestBody->_context = new Context(['nested' => $annotation], $context);
                $jsonContent = new OA\JsonContent(type: $type);
                if ('array' === $type && null !== $itemsType = $this->guessItemsType($rp, $payload)) {
                    $jsonContent->items = new OA\Items(type: $itemsType);
                }
                $jsonContent->_context = new Context(['nested' => $annotation->requestBody], $context);
                $annotation->requestBody->merge([$jsonContent]);

                return;
            }
        }
    }

    private function guessItemsType(\ReflectionParameter $parameter, MapRequestPayload $payload): ?string
    {
        return ($payload instanceof Payload ? $payload->itemsType : null)
            ?? $payload->type
            ?? CollectionItemsTypeGuesser::guess($parameter);
    }
}
