<?php

namespace Yceruto\OpenApiBundle\HttpKernel\Controller\ValueResolver\ConstraintGuesser;

use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraint;
use Yceruto\OpenApiBundle\Attribute\Path;

interface ConstraintGuesserInterface
{
    /**
     * @return list<Constraint>
     */
    public function guess(ArgumentMetadata $argument, Path $attribute): array;
}
