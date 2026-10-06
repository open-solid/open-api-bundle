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
 * Augments query and path parameter schemas from the Symfony Validator constraints declared
 * on the backing PHP property or method parameter.
 *
 * This is the parameter counterpart of AugmentSchemaConstraints. It runs later in the
 * pipeline because query parameters do not exist yet when schemas are augmented:
 * the operation guessers attach them to the operation.
 */
readonly class AugmentParameterConstraints implements ProcessorInterface
{
    public function __construct(
        private ConstraintSchemaApplier $applier = new ConstraintSchemaApplier(),
    ) {
    }

    public function __invoke(Analysis $analysis): void
    {
        /** @var OA\Parameter[] $parameters */
        $parameters = $analysis->getAnnotationsOfType(OA\Parameter::class);

        foreach ($parameters as $parameter) {
            $reflector = ConstraintSchemaApplier::resolveReflector($parameter->_context->reflector ?? null);

            // Symfony constraints can only target a property, so a plain method parameter
            // (anything but a promoted constructor property) never carries one.
            if (!$reflector instanceof \ReflectionProperty) {
                continue;
            }

            if (!Undefined::isDefault($parameter->schema)) {
                $this->applier->apply($parameter->schema, $reflector);
            }

            if (true !== $parameter->required && $this->applier->isRequired($reflector)) {
                $parameter->required = true;
            }
        }
    }
}
