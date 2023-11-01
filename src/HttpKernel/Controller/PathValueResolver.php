<?php

namespace Yceruto\OpenApiBundle\HttpKernel\Controller;

use BackedEnum;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestAttributeValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use UnitEnum;
use Yceruto\OpenApiBundle\Attribute\Path;

readonly class PathValueResolver implements ValueResolverInterface
{
    public function __construct(
        private RequestAttributeValueResolver $attributeValueResolver,
        private ValidatorInterface $validator,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        /** @var Path $attribute */
        $attribute = $argument->getAttributesOfType(Path::class, ArgumentMetadata::IS_INSTANCEOF)[0] ?? null;

        if (!$attribute || !$constraints = $this->guessConstraints($argument, $attribute)) {
            return [];
        }

        if ($argument->isVariadic()) {
            throw new \LogicException(sprintf('Mapping variadic argument "$%s" is not supported.', $argument->getName()));
        }

        if (!$type = $argument->getType()) {
            throw new \LogicException(sprintf('Could not resolve the "$%s" controller argument: argument should be typed.', $argument->getName()));
        }

        if (!is_scalar($type)) {
            throw new \LogicException(sprintf('Could not resolve the "$%s" controller argument: argument type should be a scalar type, "%s" given.', $argument->getName(), $type));
        }

        $value = $this->attributeValueResolver->resolve($request, $argument)[0] ?? null;

        if (null === $value) {
            return [];
        }

        $violations = $this->validator->validate($value, $constraints);

        if ($violations->count() > 0) {
            throw new ValidationFailedException($value, $violations);
        }

        return [$value];
    }

    /**
     * @return list<Constraint>
     */
    protected function guessConstraints(ArgumentMetadata $argument, Path $attribute): array
    {
        $constraints[] = match ($attribute->format) {
            'uuid' => new Assert\Uuid(),
            'date' => new Assert\Date(),
            'datetime' => new Assert\DateTime(),
            'locale' => new Assert\Locale(),
            'currency' => new Assert\Currency(),
            'integer' => new Assert\Type(type: 'int'),
            default => null,
        };

        $enum = $attribute->enum;

        if (is_string($enum) && is_subclass_of($enum, UnitEnum::class)) {
            $enum = $enum::cases();
        }

        if (is_array($enum) && $enum) {
            $choices = [];
            foreach ($enum as $value) {
                if ($value instanceof BackedEnum) {
                    $choices[] = $value->value;
                } elseif ($value instanceof UnitEnum) {
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
