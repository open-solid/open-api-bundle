<?php

namespace OpenSolid\OpenApiBundle\HttpKernel\Controller\ValueResolver\ConstraintGuesser;

use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraint;
use OpenSolid\OpenApiBundle\Attribute\Path;

interface ConstraintGuesserInterface
{
    /**
     * @return array<Constraint>
     */
    public function guess(ArgumentMetadata $argument, Path $attribute): array;
}
