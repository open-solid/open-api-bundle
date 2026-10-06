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
use OpenApi\Undefined;
use OpenSolid\OpenApiBundle\OpenApi\Constraint\ConstraintSchemaApplier;

/**
 * Augments schema properties from the Symfony Validator constraints declared on the
 * backing PHP property.
 *
 * See ConstraintSchemaApplier for the constraint-to-keyword mapping. Query and path
 * parameters get the same treatment from AugmentParameterConstraints.
 */
readonly class AugmentSchemaConstraints implements ProcessorInterface
{
    public function __construct(
        private ConstraintSchemaApplier $applier = new ConstraintSchemaApplier(),
    ) {
    }

    public function __invoke(Analysis $analysis): void
    {
        /** @var OA\Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(OA\Schema::class);

        foreach ($schemas as $schema) {
            if (Undefined::isDefault($schema->properties)) {
                continue;
            }

            foreach ($schema->properties as $property) {
                $reflector = ConstraintSchemaApplier::resolveReflector($property->_context->reflector ?? null);

                if (!$reflector instanceof \ReflectionProperty) {
                    continue;
                }

                $this->applier->apply($property, $reflector);
            }
        }
    }
}
