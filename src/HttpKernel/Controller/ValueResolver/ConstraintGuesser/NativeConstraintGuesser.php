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
use Symfony\Component\Validator\Constraints as Assert;

class NativeConstraintGuesser implements ConstraintGuesserInterface
{
    public function guess(ArgumentMetadata $argument, Path $attribute): array
    {
        $constraints = [match ($attribute->format) {
            'uuid' => new Assert\Uuid(),
            'date' => new Assert\Date(),
            'datetime' => new Assert\DateTime(),
            'locale' => new Assert\Locale(),
            'currency' => new Assert\Currency(),
            'numeric' => new Assert\Type(type: 'numeric'),
            default => null,
        }];

        $enum = $attribute->enum;

        if (is_string($enum) && is_subclass_of($enum, \UnitEnum::class)) {
            $enum = $enum::cases();
        }

        if (is_array($enum) && $enum) {
            $choices = [];
            foreach ($enum as $value) {
                if ($value instanceof \BackedEnum) {
                    $choices[] = $value->value;
                } elseif ($value instanceof \UnitEnum) {
                    $choices[] = $value->name;
                } else {
                    $choices[] = $value;
                }
            }
            $constraints[] = new Assert\Choice(
                choices: $choices,
                message: sprintf('This is not a valid %s value.', $argument->getName()),
            );
        }

        return array_filter($constraints);
    }
}
