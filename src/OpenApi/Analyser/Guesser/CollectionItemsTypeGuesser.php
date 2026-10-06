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

namespace OpenSolid\OpenApiBundle\OpenApi\Analyser\Guesser;

use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\BuiltinType;
use Symfony\Component\TypeInfo\Type\CollectionType;
use Symfony\Component\TypeInfo\Type\NullableType;
use Symfony\Component\TypeInfo\Type\ObjectType;
use Symfony\Component\TypeInfo\TypeIdentifier;
use Symfony\Component\TypeInfo\TypeResolver\TypeResolver;
use Symfony\Component\TypeInfo\TypeResolver\TypeResolverInterface;

/**
 * Guesses the items type of an array from its docblock, e.g. "@return list<ResourceView>"
 * or "@param ResourceView[] $payload".
 */
final class CollectionItemsTypeGuesser
{
    private static ?TypeResolverInterface $typeResolver = null;

    /**
     * @return string|null a class name or an OpenAPI scalar type, null when the items type is unknown
     */
    public static function guess(\ReflectionFunctionAbstract|\ReflectionParameter $reflector): ?string
    {
        try {
            $type = (self::$typeResolver ??= TypeResolver::create())->resolve($reflector);
        } catch (\Throwable) {
            return null;
        }

        if ($type instanceof NullableType) {
            $type = $type->getWrappedType();
        }

        if (!$type instanceof CollectionType) {
            return null;
        }

        return self::toItemsType($type->getCollectionValueType());
    }

    private static function toItemsType(Type $type): ?string
    {
        if ($type instanceof ObjectType) {
            return $type->getClassName();
        }

        if (!$type instanceof BuiltinType) {
            return null;
        }

        return match ($type->getTypeIdentifier()) {
            TypeIdentifier::INT => 'integer',
            TypeIdentifier::FLOAT => 'number',
            TypeIdentifier::BOOL => 'boolean',
            TypeIdentifier::STRING => 'string',
            default => null,
        };
    }
}
