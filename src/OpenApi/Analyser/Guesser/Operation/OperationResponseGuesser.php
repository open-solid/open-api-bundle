<?php

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\Operation;

use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Annotations\Operation;
use OpenApi\Attributes as OA;
use OpenApi\Context;
use OpenApi\Generator;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\AnalyserGuesserInterface;

class OperationResponseGuesser implements AnalyserGuesserInterface
{
    public function guess(\Reflector $reflector, AbstractAnnotation $annotation, Context $context): void
    {
        if (!$reflector instanceof \ReflectionMethod || !$annotation instanceof Operation) {
            return;
        }

        if (!Generator::isDefault($annotation->responses)) {
            return;
        }

        $isVoid = (null === $rrt = $reflector->getReturnType()) || !$rrt instanceof \ReflectionNamedType || ($rrt->isBuiltin() && 'array' !== $rrt->getName());
        $isPost = $annotation instanceof OA\Post;
        $isMutable = $annotation instanceof OA\Post || $annotation instanceof OA\Put || $annotation instanceof OA\Patch;
        $isResourceAware = ($annotation instanceof OA\Get || $annotation instanceof OA\Put || $annotation instanceof OA\Patch || $annotation instanceof OA\Delete) && 'array' !== $rrt?->getName();

        if ($isVoid) {
            $statusCode = 204;
        } else {
            $statusCode = $isPost ? 201 : 200;
        }
        $successResponse = new OA\Response(response: $statusCode, description: 'Successful');
        $successResponse->_context = new Context(['nested' => $annotation], $context);
        if (!$isVoid) {
            $jsonContent = new OA\JsonContent(type: $rrt->getName());
            $jsonContent->_context = new Context(['nested' => $successResponse], $context);
            if (property_exists($annotation, 'itemsType') && null !== $annotation->itemsType && 'array' === $rrt->getName()) {
                $jsonContent->items = new OA\Items(type: $annotation->itemsType);
            }
            $successResponse->merge([$jsonContent]);
        }

        $responses = [$successResponse];

        $notFoundErrorResponse = new OA\Response(ref: '#/components/responses/400', response: 400);
        $notFoundErrorResponse->_context = new Context(['nested' => $annotation], $context);
        $responses[] = $notFoundErrorResponse;

        if ($isResourceAware) {
            $notFoundErrorResponse = new OA\Response(ref: '#/components/responses/404', response: 404);
            $notFoundErrorResponse->_context = new Context(['nested' => $annotation], $context);
            $responses[] = $notFoundErrorResponse;
        }

        if ($isMutable) {
            $validationErrorResponse = new OA\Response(ref: '#/components/responses/422', response: 422);
            $validationErrorResponse->_context = new Context(['nested' => $annotation], $context);
            $responses[] = $validationErrorResponse;
        }

        $annotation->responses = $responses;
    }
}
