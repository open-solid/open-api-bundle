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
use OpenApi\Annotations as OAA;
use OpenApi\Annotations\Operation;
use OpenApi\Attributes as OA;
use OpenApi\Context;
use OpenApi\Undefined;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\AnalyserGuesserInterface;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\CollectionItemsTypeGuesser;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guesses the operation responses from the controller signature.
 *
 * The success response is inferred from the return type. When the operation declares no
 * responses, the common error responses are added too. When it declares some, the inferred
 * success response is merged into them: a declared status code is never replaced, but it
 * receives the inferred body when it declares none.
 *
 * A secured operation also documents the 401 and 403 responses.
 */
class OperationResponseGuesser implements AnalyserGuesserInterface
{
    public function guess(\Reflector $reflector, AbstractAnnotation $annotation, Context $context): void
    {
        if (!$reflector instanceof \ReflectionMethod || !$annotation instanceof Operation) {
            return;
        }

        $declared = !Undefined::isDefault($annotation->responses);
        $candidates = [$this->createSuccessResponse($reflector, $annotation, $context)];

        if (!$declared) {
            $returnType = $reflector->getReturnType();
            $returnsCollection = $returnType instanceof \ReflectionNamedType && 'array' === $returnType->getName();
            $isResourceAware = !$returnsCollection && ($annotation instanceof OA\Get || $annotation instanceof OA\Head || $annotation instanceof OA\Put || $annotation instanceof OA\Patch || $annotation instanceof OA\Delete);
            $isMutable = $annotation instanceof OA\Post || $annotation instanceof OA\Put || $annotation instanceof OA\Patch;

            $candidates[] = $this->createRefResponse(400, $annotation, $context);
            if ($isResourceAware) {
                $candidates[] = $this->createRefResponse(404, $annotation, $context);
            }
            if ($isMutable) {
                $candidates[] = $this->createRefResponse(422, $annotation, $context);
            }
        }

        if (!Undefined::isDefault($annotation->security) && [] !== $annotation->security) {
            $candidates[] = $this->createRefResponse(401, $annotation, $context);
            $candidates[] = $this->createRefResponse(403, $annotation, $context);
        }

        $annotation->responses = $this->merge($declared ? $annotation->responses : [], $candidates);
    }

    private function createSuccessResponse(\ReflectionMethod $reflector, Operation $operation, Context $context): OA\Response
    {
        $returnType = $reflector->getReturnType();
        $isVoid = !$returnType instanceof \ReflectionNamedType || ($returnType->isBuiltin() && 'array' !== $returnType->getName());
        $isResponse = !$isVoid && is_a($returnType->getName(), Response::class, true);

        $statusCode = property_exists($operation, 'statusCode') && null !== $operation->statusCode
            ? $operation->statusCode
            : ($isVoid ? 204 : ($operation instanceof OA\Post ? 201 : 200));

        $response = new OA\Response(response: $statusCode, description: 'Successful');
        $response->_context = new Context(['nested' => $operation], $context);

        if ($isVoid || $isResponse || $operation instanceof OA\Head || 204 === $statusCode) {
            return $response;
        }

        $jsonContent = new OA\JsonContent(type: $returnType->getName());
        $jsonContent->_context = new Context(['nested' => $response], $context);
        if ('array' === $returnType->getName()) {
            $itemsType = (property_exists($operation, 'itemsType') ? $operation->itemsType : null) ?? CollectionItemsTypeGuesser::guess($reflector);
            if (null !== $itemsType) {
                $jsonContent->items = new OA\Items(type: $itemsType);
            }
        }
        $response->merge([$jsonContent]);

        return $response;
    }

    private function createRefResponse(int $statusCode, Operation $operation, Context $context): OA\Response
    {
        $response = new OA\Response(ref: '#/components/responses/'.$statusCode, response: $statusCode);
        $response->_context = new Context(['nested' => $operation], $context);

        return $response;
    }

    /**
     * @param array<OAA\Response> $responses
     * @param array<OAA\Response> $candidates
     *
     * @return array<OAA\Response>
     */
    private function merge(array $responses, array $candidates): array
    {
        foreach ($candidates as $candidate) {
            foreach ($responses as $response) {
                if ((string) $response->response !== (string) $candidate->response) {
                    continue;
                }

                if (!$this->hasContent($response) && $this->hasContent($candidate)) {
                    foreach ($candidate->_unmerged as $content) {
                        $content->_context->nested = $response;
                    }
                    $response->merge($candidate->_unmerged);
                }

                continue 2;
            }

            $responses[] = $candidate;
        }

        usort($responses, static fn (OAA\Response $a, OAA\Response $b): int => (is_numeric($a->response) ? (int) $a->response : \PHP_INT_MAX) <=> (is_numeric($b->response) ? (int) $b->response : \PHP_INT_MAX));

        return $responses;
    }

    private function hasContent(OAA\Response $response): bool
    {
        if (!Undefined::isDefault($response->ref) || (!Undefined::isDefault($response->content) && [] !== $response->content)) {
            return true;
        }

        foreach ($response->_unmerged as $annotation) {
            if ($annotation instanceof OAA\MediaType || $annotation instanceof OAA\JsonContent || $annotation instanceof OAA\XmlContent) {
                return true;
            }
        }

        return false;
    }
}
