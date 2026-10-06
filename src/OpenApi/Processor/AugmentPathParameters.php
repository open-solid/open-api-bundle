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
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Undefined;
use OpenSolid\OpenApiBundle\Attribute\Path;

/**
 * Moves the "format" and "enum" shortcuts of #[Path] into the parameter schema.
 */
readonly class AugmentPathParameters implements ProcessorInterface
{
    public function __invoke(Analysis $analysis): void
    {
        /** @var Path[] $parameters */
        $parameters = $analysis->getAnnotationsOfType(Path::class);

        foreach ($parameters as $parameter) {
            if (Undefined::isDefault($parameter->format, $parameter->enum)) {
                continue;
            }

            if (Undefined::isDefault($parameter->schema)) {
                $parameter->schema = new OA\Schema(['_context' => new Context(['generated' => true], $parameter->_context)]);
            }

            if (Undefined::isDefault($parameter->schema->format) && !Undefined::isDefault($parameter->format)) {
                $parameter->schema->format = $parameter->format;
            }

            if (Undefined::isDefault($parameter->schema->enum) && !Undefined::isDefault($parameter->enum)) {
                $parameter->schema->enum = $this->enumValues($parameter->enum);
            }
        }
    }

    /**
     * @param array<mixed>|class-string $enum
     *
     * @return array<mixed>
     */
    private function enumValues(array|string $enum): array
    {
        if (\is_string($enum)) {
            $enum = is_subclass_of($enum, \UnitEnum::class) ? $enum::cases() : [$enum];
        }

        return array_map(static fn (mixed $value): mixed => match (true) {
            $value instanceof \BackedEnum => $value->value,
            $value instanceof \UnitEnum => $value->name,
            default => $value,
        }, array_values($enum));
    }
}
