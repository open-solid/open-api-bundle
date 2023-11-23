<?php

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser;

use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Context;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

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
            $annotations = $factory->build($reflector, $annotations, $context);
        }

        return $annotations;
    }
}
