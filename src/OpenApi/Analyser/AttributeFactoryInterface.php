<?php

namespace Yceruto\OpenApiBundle\OpenApi\Analyser;

use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Context;

interface AttributeFactoryInterface
{
    /**
     * @param array<AbstractAnnotation> $annotations
     *
     * @return array<AbstractAnnotation>
     */
    public function build(\Reflector $reflector, array $annotations, Context $context): array;
}
