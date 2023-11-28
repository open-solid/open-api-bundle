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

    /**
     * @param iterable<AnalyserResolverInterface> $resolvers
     */
    public function __construct(private readonly iterable $resolvers)
    {
    }

    public function setGenerator(Generator $generator): void
    {
        $this->generator = $generator;
    }

    public function fromFile(string $filename, Context $context): Analysis
    {
        return $this->resolve($filename)->fromFile($filename, $context);
    }

    protected function resolve(string $filename): AnalyserInterface
    {
        $format = pathinfo($filename, PATHINFO_EXTENSION);

        foreach ($this->resolvers as $resolver) {
            if (null !== $analyser = $resolver->resolve($filename, $format)) {
                $analyser->setGenerator($this->generator);

                return $analyser;
            }
        }

        throw new \InvalidArgumentException(sprintf('No analyser found for file "%s".', $filename));
    }
}
