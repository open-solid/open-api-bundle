<?php

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Resolver;

use OpenApi\Analysers\AnalyserInterface;

readonly class AnalyserResolverChain implements AnalyserResolverInterface
{
    /**
     * @param iterable<AnalyserResolverInterface> $resolvers
     */
    public function __construct(private iterable $resolvers)
    {
    }

    public function resolve(string $filename, string $format): ?AnalyserInterface
    {
        foreach ($this->resolvers as $resolver) {
            if (null !== $analyser = $resolver->resolve($filename, $format)) {
                return $analyser;
            }
        }

        return null;
    }
}
