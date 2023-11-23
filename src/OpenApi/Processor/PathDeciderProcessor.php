<?php

namespace Yceruto\OpenApiBundle\OpenApi\Processor;

use OpenApi\Analysis;
use OpenApi\Annotations\Operation;
use OpenApi\Generator;
use OpenApi\Processors\ProcessorInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Yceruto\OpenApiBundle\Routing\Attribute\Delete;
use Yceruto\OpenApiBundle\Routing\Attribute\Get;
use Yceruto\OpenApiBundle\Routing\Attribute\Patch;
use Yceruto\OpenApiBundle\Routing\Attribute\Post;
use Yceruto\OpenApiBundle\Routing\Attribute\Put;

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
        $el = new ExpressionLanguage(null, iterator_to_array($this->expressionLanguageProviders));

        foreach ($analysis->openapi->paths as $index => $pathItem) {
            /** @var Operation[]|Post[]|Get[]|Put[]|Patch[]|Delete[] $methods */
            $methods = [
                $pathItem->post,
                $pathItem->get,
                $pathItem->put,
                $pathItem->patch,
                $pathItem->delete,
            ];

            foreach ($methods as $method) {
                if (Generator::isDefault($method) || Generator::isDefault($method->operationId)) {
                    continue;
                }

                if (null === $method->when) {
                    continue;
                }

                try {
                    if (!$el->evaluate($method->when, ['context' => $this->requestContext])) {
                        throw new ResourceNotFoundException();
                    }
                } catch (ResourceNotFoundException) {
                    $analysis->openapi->paths[$index]->{$method->method} = Generator::UNDEFINED;
                    $this->detachAnnotationRecursively($method, $analysis);
                }
            }

            if (Generator::isDefault($pathItem->post)
                && Generator::isDefault($pathItem->get)
                && Generator::isDefault($pathItem->put)
                && Generator::isDefault($pathItem->patch)
                && Generator::isDefault($pathItem->delete)) {
                unset($analysis->openapi->paths[$index]);
                $this->detachAnnotationRecursively($pathItem, $analysis);
            }
        }
    }
}
