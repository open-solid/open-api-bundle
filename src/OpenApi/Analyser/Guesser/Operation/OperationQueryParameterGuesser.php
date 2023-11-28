<?php

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\Operation;

use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Annotations\Operation;
use OpenApi\Context;
use OpenApi\Generator;
use OpenSolid\OpenApiBundle\Attribute\Param;
use OpenSolid\OpenApiBundle\Attribute\Query;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\AnalyserGuesserInterface;

class OperationQueryParameterGuesser implements AnalyserGuesserInterface
{
    public function guess(\Reflector $reflector, AbstractAnnotation $annotation, Context $context): void
    {
        if (!$reflector instanceof \ReflectionMethod || !$annotation instanceof Operation) {
            return;
        }

        if (!Generator::isDefault($annotation->parameters)) {
            return;
        }

        $parameters = [];
        foreach ($reflector->getParameters() as $rp) {
            foreach ($rp->getAttributes(Query::class, \ReflectionAttribute::IS_INSTANCEOF) as $_) {
                if ((null === $rnt = $rp->getType()) || !$rnt instanceof \ReflectionNamedType || $rnt->isBuiltin() || !class_exists($rnt->getName())) {
                    continue;
                }

                $this->guessRecursive($rnt->getName(), $parameters, $annotation, $context);
            }
        }

        if ($parameters) {
            $annotation->parameters = $parameters;
        }
    }

    protected function guessRecursive(string $class, array &$parameters, Operation $operation, Context $context, string $parent = null): void
    {
        $reflectionClass = new \ReflectionClass($class);

        foreach ($reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $propertyReflector) {
            if (null === $propertyType = $propertyReflector->getType()) {
                continue;
            }

            if ($propertyType instanceof \ReflectionNamedType && !$propertyType->isBuiltin()) {
                $parentName = $parent ? $parent.'[' : '';
                $parentName .= $propertyReflector->getName();
                $parentName .= $parent ? ']' : '';

                $this->guessRecursive($propertyType->getName(), $parameters, $operation, $context, $parentName);

                continue;
            }

            foreach ($propertyReflector->getAttributes(Param::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                $parameter = $attribute->newInstance();

                if (Generator::isDefault($parameter->name)) {
                    $parameter->name = $parent ? $parent.'[' : '';
                    $parameter->name .= $propertyReflector->getName();
                    $parameter->name .= $parent ? ']' : '';
                    $parameter->parameter = $parameter->name;
                }

                if ($propertyReflector->isDefault()) {
                    $defaultValue = $propertyReflector->getDefaultValue();
                    if (null !== $defaultValue && '' !== $defaultValue) {
                        $parameter->example = $defaultValue;
                    }
                }

                if (!$propertyType->allowsNull()) {
                    $parameter->required = true;
                }

                $parameter->_context = new Context(['nested' => $operation], $context);
                $parameters[] = $parameter;
            }
        }
    }
}
