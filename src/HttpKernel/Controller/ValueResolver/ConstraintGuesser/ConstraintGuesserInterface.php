<?php

namespace OpenSolid\OpenApiBundle\HttpKernel\Controller\ValueResolver\ConstraintGuesser;

use OpenSolid\OpenApiBundle\Attribute\Path;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraint;

interface ConstraintGuesserInterface
{
    /**
     * @return array<Constraint>
     */
    public function guess(ArgumentMetadata $argument, Path $attribute): array;
}
