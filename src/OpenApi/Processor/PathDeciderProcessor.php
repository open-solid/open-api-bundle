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

namespace OpenSolid\OpenApiBundle\OpenApi\Processor;

use OpenApi\Analysis;
use OpenApi\Undefined;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

readonly class PathDeciderProcessor implements ProcessorInterface
{
    use ProcessorTrait;

    public function __construct(
        private iterable $expressionLanguageProviders,
        private RequestContext $requestContext,
    ) {
    }

    public function __invoke(Analysis $analysis): void
    {
        if (Undefined::isDefault($analysis->openapi->paths)) {
            return;
        }

        $el = new ExpressionLanguage(null, iterator_to_array($this->expressionLanguageProviders));

        foreach ($analysis->openapi->paths as $index => $pathItem) {
            foreach ($this->operationsOf($pathItem) as $method) {
                if (!property_exists($method, 'when') || null === $method->when) {
                    continue;
                }

                try {
                    if (!$el->evaluate($method->when, ['context' => $this->requestContext])) {
                        throw new ResourceNotFoundException();
                    }
                } catch (ResourceNotFoundException) {
                    $analysis->openapi->paths[$index]->{$method->method} = Undefined::UNDEFINED;
                    $this->detachAnnotationRecursively($method, $analysis);
                }
            }

            if ([] === $this->operationsOf($pathItem)) {
                unset($analysis->openapi->paths[$index]);
                $this->detachAnnotationRecursively($pathItem, $analysis);
            }
        }
    }
}
