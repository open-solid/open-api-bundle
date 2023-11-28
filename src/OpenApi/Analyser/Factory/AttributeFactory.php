<?php

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Factory;

use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Context;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser\AnalyserGuesserInterface;

class AttributeFactory extends AttributeAnnotationFactory
{
    /**
     * @param iterable<AnalyserGuesserInterface> $guessers
     */
    public function __construct(private readonly iterable $guessers)
    {
    }

    public function build(\Reflector $reflector, Context $context): array
    {
        $annotations = parent::build($reflector, $context);

        foreach ($annotations as $annotation) {
            foreach ($this->guessers as $guesser) {
                $guesser->guess($reflector, $annotation, $context);
            }
        }

        return $annotations;
    }
}
