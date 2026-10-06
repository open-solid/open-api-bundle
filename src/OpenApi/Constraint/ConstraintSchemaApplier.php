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

namespace OpenSolid\OpenApiBundle\OpenApi\Constraint;

use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Undefined;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Maps Symfony Validator constraints onto an OpenAPI schema:
 * - `format` from Uuid, Ulid, Email, Url, Hostname, Ip, Date, DateTime, Time
 * - `minLength` / `maxLength` from Length, and `minLength` from NotBlank on a string
 * - `minimum` / `maximum` / `exclusiveMinimum` / `exclusiveMaximum` from Range and comparison constraints
 * - `pattern` from Regex
 * - `enum` from Choice
 * - `minItems` / `maxItems` from Count
 * - the same keywords on `items` from the nested constraints of All
 *
 * Shared by the schema, parameter, and required-field processors so that a body property
 * and a query parameter infer the same metadata from the same constraints.
 */
readonly class ConstraintSchemaApplier
{
    /**
     * Resolves the reflector that carries the constraint attributes.
     *
     * A promoted constructor property is reported as a ReflectionParameter, but the
     * attributes are readable from the declared property, so it is upgraded here.
     *
     * Only a promoted constructor property is upgraded. Any other method parameter keeps
     * its own reflector: a plain argument that shares its name with a property of the
     * declaring class, such as an __invoke() argument and an injected dependency, must not
     * inherit the constraints of that property.
     */
    public static function resolveReflector(?\Reflector $reflector): \ReflectionProperty|\ReflectionParameter|null
    {
        if ($reflector instanceof \ReflectionParameter && $reflector->isPromoted()) {
            // only a constructor parameter can be promoted
            return $reflector->getDeclaringClass()?->getProperty($reflector->getName());
        }

        return $reflector instanceof \ReflectionProperty || $reflector instanceof \ReflectionParameter
            ? $reflector
            : null;
    }

    public function apply(OA\Schema $schema, \ReflectionProperty $reflector): void
    {
        $this->applyConstraints($schema, $this->constraints($reflector), $reflector);
    }

    /**
     * @param iterable<Constraint>     $constraints
     * @param \ReflectionProperty|null $reflector   The property that declares the constraints, or
     *                                              null for the nested constraints of an All
     *                                              constraint, which describe an item instead
     */
    private function applyConstraints(OA\Schema $schema, iterable $constraints, ?\ReflectionProperty $reflector): void
    {
        $hasNotBlank = false;

        foreach ($constraints as $constraint) {
            // NotBlank only rules out the empty string, so it must not win over a Length that
            // asks for more. It is applied last, whatever the order of the attributes.
            if ($constraint instanceof Assert\NotBlank) {
                $hasNotBlank = true;

                continue;
            }

            match (true) {
                $constraint instanceof Assert\Uuid => $this->applyFormat($schema, 'uuid'),
                $constraint instanceof Assert\Ulid => $this->applyFormat($schema, 'ulid'),
                $constraint instanceof Assert\Email => $this->applyFormat($schema, 'email'),
                $constraint instanceof Assert\Url => $this->applyFormat($schema, 'uri'),
                $constraint instanceof Assert\Hostname => $this->applyFormat($schema, 'hostname'),
                $constraint instanceof Assert\Ip => $this->applyIpFormat($schema, $constraint),
                $constraint instanceof Assert\Date => $this->applyFormat($schema, 'date'),
                $constraint instanceof Assert\DateTime => $this->applyFormat($schema, 'date-time'),
                $constraint instanceof Assert\Time => $this->applyFormat($schema, 'time'),
                $constraint instanceof Assert\Length => $this->applyLength($schema, $constraint),
                $constraint instanceof Assert\All => $this->applyAll($schema, $constraint),
                $constraint instanceof Assert\Count => $this->applyCount($schema, $constraint),
                $constraint instanceof Assert\Range => $this->applyRange($schema, $constraint),
                $constraint instanceof Assert\Positive,
                $constraint instanceof Assert\PositiveOrZero,
                $constraint instanceof Assert\Negative,
                $constraint instanceof Assert\NegativeOrZero,
                $constraint instanceof Assert\GreaterThan,
                $constraint instanceof Assert\GreaterThanOrEqual,
                $constraint instanceof Assert\LessThan,
                $constraint instanceof Assert\LessThanOrEqual => $this->applyComparison($schema, $constraint),
                $constraint instanceof Assert\Regex => $this->applyPattern($schema, $constraint),
                $constraint instanceof Assert\Choice => $this->applyChoice($schema, $constraint),
                default => null,
            };
        }

        if ($hasNotBlank) {
            $this->applyNotBlank($schema, $reflector);
        }
    }

    /**
     * Whether the constraints make the value mandatory, regardless of PHP type nullability
     * or default values.
     *
     * NotBlank accepts null when allowNull is set, so it leaves the value optional.
     */
    public function isRequired(\ReflectionProperty $reflector): bool
    {
        foreach ($this->constraints($reflector) as $constraint) {
            if ($constraint instanceof Assert\NotNull) {
                return true;
            }

            if ($constraint instanceof Assert\NotBlank && !$constraint->allowNull) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return iterable<Constraint>
     */
    private function constraints(\ReflectionProperty $reflector): iterable
    {
        foreach ($reflector->getAttributes(Constraint::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            yield $attribute->newInstance();
        }
    }

    private function applyFormat(OA\Schema $schema, string $format): void
    {
        if (Undefined::isDefault($schema->format)) {
            $schema->format = $format;
        }
    }

    private function applyIpFormat(OA\Schema $schema, Assert\Ip $constraint): void
    {
        if (Undefined::isDefault($schema->format)) {
            $schema->format = str_starts_with($constraint->version, '6') ? 'ipv6' : 'ipv4';
        }
    }

    private function applyLength(OA\Schema $schema, Assert\Length $constraint): void
    {
        if (null !== $constraint->min && Undefined::isDefault($schema->minLength)) {
            $schema->minLength = $constraint->min;
        }

        if (null !== $constraint->max && Undefined::isDefault($schema->maxLength)) {
            $schema->maxLength = $constraint->max;
        }
    }

    /**
     * NotBlank rejects the empty string, which is `minLength: 1` for a string value.
     *
     * The PHP type decides, not the schema type: this can run before type inference. An item
     * has no reflector of its own, so there the schema type decides.
     */
    private function applyNotBlank(OA\Schema $schema, ?\ReflectionProperty $reflector): void
    {
        if (null === $reflector) {
            if ('string' !== $schema->type) {
                return;
            }
        } else {
            $type = $reflector->getType();

            if (!$type instanceof \ReflectionNamedType || 'string' !== $type->getName()) {
                return;
            }
        }

        if (Undefined::isDefault($schema->minLength)) {
            $schema->minLength = 1;
        }
    }

    /**
     * An All constraint validates every element of a collection, so its nested constraints
     * describe the items of the array, not the array itself.
     *
     * The item type is left alone: a constraint carries none. When the schema has no items yet,
     * an empty one is created, and the swagger-php type resolver fills its type in later from
     * the `@var` line of the property.
     */
    private function applyAll(OA\Schema $schema, Assert\All $constraint): void
    {
        if (!Undefined::isDefault($schema->type) && 'array' !== $schema->type) {
            return;
        }

        if (Undefined::isDefault($schema->items)) {
            $schema->items = new OA\Items([
                '_context' => new Context(['generated' => true], $schema->_context),
            ]);
        }

        $this->applyConstraints(
            $schema->items,
            \is_array($constraint->constraints) ? $constraint->constraints : [$constraint->constraints],
            null,
        );
    }

    private function applyCount(OA\Schema $schema, Assert\Count $constraint): void
    {
        if (null !== $constraint->min && Undefined::isDefault($schema->minItems)) {
            $schema->minItems = $constraint->min;
        }

        if (null !== $constraint->max && Undefined::isDefault($schema->maxItems)) {
            $schema->maxItems = $constraint->max;
        }
    }

    private function applyRange(OA\Schema $schema, Assert\Range $constraint): void
    {
        if (null !== $constraint->min && is_numeric($constraint->min) && Undefined::isDefault($schema->minimum)) {
            $schema->minimum = $constraint->min + 0;
        }

        if (null !== $constraint->max && is_numeric($constraint->max) && Undefined::isDefault($schema->maximum)) {
            $schema->maximum = $constraint->max + 0;
        }
    }

    private function applyComparison(OA\Schema $schema, Assert\AbstractComparison $constraint): void
    {
        if (!is_numeric($constraint->value)) {
            return;
        }

        $value = $constraint->value + 0;

        match (true) {
            $constraint instanceof Assert\Positive,
            $constraint instanceof Assert\GreaterThan => Undefined::isDefault($schema->exclusiveMinimum) && $schema->exclusiveMinimum = $value,
            $constraint instanceof Assert\PositiveOrZero,
            $constraint instanceof Assert\GreaterThanOrEqual => Undefined::isDefault($schema->minimum) && $schema->minimum = $value,
            $constraint instanceof Assert\Negative,
            $constraint instanceof Assert\LessThan => Undefined::isDefault($schema->exclusiveMaximum) && $schema->exclusiveMaximum = $value,
            $constraint instanceof Assert\NegativeOrZero,
            $constraint instanceof Assert\LessThanOrEqual => Undefined::isDefault($schema->maximum) && $schema->maximum = $value,
        };
    }

    private function applyPattern(OA\Schema $schema, Assert\Regex $constraint): void
    {
        if (!$constraint->match || !Undefined::isDefault($schema->pattern)) {
            return;
        }

        $pattern = $constraint->pattern;

        if (null === $pattern || '' === $pattern) {
            return;
        }

        // Strip PHP regex delimiters (e.g., /^pattern$/ → ^pattern$)
        $delimiter = $pattern[0];
        $endPos = strrpos($pattern, $delimiter);

        if ($endPos > 0) {
            $pattern = substr($pattern, 1, $endPos - 1);
        }

        $schema->pattern = $pattern;
    }

    private function applyChoice(OA\Schema $schema, Assert\Choice $constraint): void
    {
        if ($constraint->multiple || null === $constraint->choices || !Undefined::isDefault($schema->enum)) {
            return;
        }

        $schema->enum = $constraint->choices;
    }
}
