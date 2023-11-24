<?php

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser;

use OpenApi\Analysers\AnalyserInterface;
use OpenApi\Analysis;
use OpenApi\Context;
use OpenApi\Generator;
use OpenSolid\OpenApiBundle\OpenApi\Analyser\Resolver\AnalyserResolverInterface;

class ResolvableAnalyser implements AnalyserInterface
{
    private Generator $generator;

    public function __construct(private readonly AnalyserResolverInterface $resolver)
    {
    }

    public function setGenerator(Generator $generator): void
    {
        $this->generator = $generator;
    }

    public function fromFile(string $filename, Context $context): Analysis
    {
        $analyser = $this->resolver->resolve($filename, pathinfo($filename, PATHINFO_EXTENSION));

        if (null === $analyser) {
            throw new \InvalidArgumentException(sprintf('No analyser found for file "%s".', $filename));
        }

        $analyser->setGenerator($this->generator);

        return $analyser->fromFile($filename, $context);
    }
}
