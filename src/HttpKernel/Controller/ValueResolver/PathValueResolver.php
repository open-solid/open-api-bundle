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

namespace OpenSolid\OpenApiBundle\HttpKernel\Controller\ValueResolver;

use OpenSolid\OpenApiBundle\Attribute\Path;
use OpenSolid\OpenApiBundle\HttpKernel\Controller\ValueResolver\ConstraintGuesser\ConstraintGuesserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestAttributeValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class PathValueResolver implements ValueResolverInterface
{
    /**
     * @param iterable<ConstraintGuesserInterface> $constraintGuessers
     */
    public function __construct(
        private RequestAttributeValueResolver $attributeValueResolver,
        private ValidatorInterface $validator,
        private iterable $constraintGuessers,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->isVariadic()) {
            throw new \LogicException(sprintf('Mapping variadic argument "$%s" is not supported.', $argument->getName()));
        }

        $value = $this->attributeValueResolver->resolve($request, $argument)[0] ?? null;

        if (null === $value) {
            return [];
        }

        $this->validateValue($argument, $value);

        return [$value];
    }

    /**
     * @return array<Constraint>
     */
    private function guessConstraints(ArgumentMetadata $argument, Path $attribute): array
    {
        $constraints = [[]];
        foreach ($this->constraintGuessers as $guesser) {
            $constraints[] = $guesser->guess($argument, $attribute);
        }

        return array_merge(...$constraints);
    }

    private function validateValue(ArgumentMetadata $argument, mixed $value): void
    {
        $attribute = $argument->getAttributesOfType(Path::class, ArgumentMetadata::IS_INSTANCEOF)[0] ?? null;

        if (!$attribute instanceof Path) {
            return;
        }

        if ([] === $constraints = $this->guessConstraints($argument, $attribute)) {
            return;
        }

        $violations = $this->validator->validate($value, $constraints);

        if ($violations->count() > 0) {
            throw new ValidationFailedException($value, $violations);
        }
    }
}
