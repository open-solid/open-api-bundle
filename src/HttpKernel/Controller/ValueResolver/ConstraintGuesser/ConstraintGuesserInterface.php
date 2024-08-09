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
