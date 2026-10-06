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
use OpenApi\Annotations\Parameter;
use OpenApi\Attributes\QueryParameter;
use OpenApi\Context;
use OpenApi\Undefined;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\AnalyserGuesserInterface;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;

/**
 * Guesses the operation query parameters from the method signature:
 *  - #[MapQueryString] (or #[Query]): every DTO property with a #[QueryParameter] (or #[Param])
 *    attribute becomes a query parameter, nested DTOs use the "parent[child]" notation;
 *  - #[MapQueryParameter]: the argument itself becomes a query parameter.
 *
 * Type, format, default, description and required are inferred later by AugmentQueryParameters.
 */
class OperationQueryParameterGuesser implements AnalyserGuesserInterface
{
    public function guess(\Reflector $reflector, AbstractAnnotation $annotation, Context $context): void
    {
        if (!$reflector instanceof \ReflectionMethod || !$annotation instanceof Operation) {
            return;
        }

        $parameters = Undefined::isDefault($annotation->parameters) ? [] : $annotation->parameters;
        $count = \count($parameters);

        foreach ($reflector->getParameters() as $rp) {
            if ($attribute = $rp->getAttributes(MapQueryString::class, \ReflectionAttribute::IS_INSTANCEOF)[0] ?? null) {
                $rnt = $rp->getType();

                if ($rnt instanceof \ReflectionNamedType && !$rnt->isBuiltin() && class_exists($rnt->getName())) {
                    $this->guessRecursive($rnt->getName(), $parameters, $annotation, $context, $attribute->newInstance()->key);
                }

                continue;
            }

            if ($attribute = $rp->getAttributes(MapQueryParameter::class, \ReflectionAttribute::IS_INSTANCEOF)[0] ?? null) {
                $this->guessQueryParameter($rp, $attribute->newInstance(), $parameters, $annotation, $context);
            }
        }

        if (\count($parameters) > $count) {
            $annotation->parameters = $parameters;
        }
    }

    /**
     * @param array<Parameter> $parameters
     */
    protected function guessRecursive(string $class, array &$parameters, Operation $operation, Context $context, ?string $parent = null): void
    {
        $reflectionClass = new \ReflectionClass($class);

        foreach ($reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $propertyReflector) {
            $propertyType = $propertyReflector->getType();
            $name = null === $parent ? $propertyReflector->getName() : $parent.'['.$propertyReflector->getName().']';

            $attributes = $propertyReflector->getAttributes(QueryParameter::class, \ReflectionAttribute::IS_INSTANCEOF);

            // a property without #[QueryParameter] typed with a class is a nested query DTO
            if ([] === $attributes && $propertyType instanceof \ReflectionNamedType && !$propertyType->isBuiltin() && class_exists($propertyType->getName())) {
                $this->guessRecursive($propertyType->getName(), $parameters, $operation, $context, $name);

                continue;
            }

            foreach ($attributes as $attribute) {
                $parameter = $attribute->newInstance();

                if (Undefined::isDefault($parameter->name)) {
                    $parameter->name = $name;
                }

                if ($this->has($parameters, $parameter->name)) {
                    continue;
                }

                $parameter->_context = new Context([
                    'nested' => $operation,
                    'property' => $propertyReflector->getName(),
                    'reflector' => $propertyReflector,
                    'comment' => $propertyReflector->getDocComment() ?: null,
                ], $context);
                $parameters[] = $parameter;
            }
        }
    }

    /**
     * @param array<Parameter> $parameters
     */
    protected function guessQueryParameter(\ReflectionParameter $rp, MapQueryParameter $attribute, array &$parameters, Operation $operation, Context $context): void
    {
        $name = $attribute->name ?? $rp->getName();

        // an explicit #[QueryParameter] on the same argument is already part of the operation
        if ($this->has($parameters, $name) || $rp->getAttributes(Parameter::class, \ReflectionAttribute::IS_INSTANCEOF)) {
            return;
        }

        $parameter = new QueryParameter(name: $name);
        $parameter->_context = new Context([
            'nested' => $operation,
            'reflector' => $rp,
            'comment' => null,
        ], $context);
        $parameters[] = $parameter;
    }

    /**
     * @param array<Parameter> $parameters
     */
    private function has(array $parameters, string $name): bool
    {
        foreach ($parameters as $parameter) {
            if ('query' === $parameter->in && $parameter->name === $name) {
                return true;
            }
        }

        return false;
    }
}
