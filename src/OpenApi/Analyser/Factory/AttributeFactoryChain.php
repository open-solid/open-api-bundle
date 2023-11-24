<?php

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Factory;

use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Context;

class AttributeFactoryChain extends AttributeAnnotationFactory
{
    /**
     * @param iterable<AttributeFactoryInterface> $factories
     */
    public function __construct(private readonly iterable $factories)
    {
    }

    public function build(\Reflector $reflector, Context $context): array
    {
        $annotations = parent::build($reflector, $context);

        foreach ($this->factories as $factory) {
            foreach ($factory->build($reflector, $annotations, $context) as $annotation) {
                $annotations[] = $annotation;
            }
        }

        return $annotations;
    }
}
