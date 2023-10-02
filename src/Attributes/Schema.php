<?php

namespace Yceruto\OpenApiBundle\Attributes;

use OpenApi\Attributes\AdditionalProperties;
use OpenApi\Attributes\Attachable;
use OpenApi\Attributes\Discriminator;
use OpenApi\Attributes\ExternalDocumentation;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Xml;
use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Schema extends \OpenApi\Attributes\Schema
{
    /**
     * @param string|class-string|object|null                               $ref
     * @param string[]                                                      $required
     * @param Property[]                                                    $properties
     * @param int|float                                                     $maximum
     * @param int|float                                                     $minimum
     * @param string[]|int[]|float[]|\UnitEnum[]|class-string               $enum
     * @param array<\OpenApi\Attributes\Schema|\OpenApi\Annotations\Schema> $allOf
     * @param array<Schema|\OpenApi\Annotations\Schema>                     $anyOf
     * @param array<Schema|\OpenApi\Annotations\Schema>                     $oneOf
     * @param mixed                                                         $const
     * @param array<string,mixed>|null                                      $x
     * @param Attachable[]|null                                             $attachables
     */
    public function __construct(
        // schema
        string|object|null $ref = null,
        ?string $schema = null,
        ?string $title = null,
        ?string $description = null,
        ?int $maxProperties = null,
        ?int $minProperties = null,
        ?array $required = null,
        ?array $properties = null,
        ?string $type = null,
        ?string $format = null,
        ?Items $items = null,
        ?string $collectionFormat = null,
        mixed $default = Generator::UNDEFINED,
        $maximum = null,
        ?bool $exclusiveMaximum = null,
        $minimum = null,
        ?bool $exclusiveMinimum = null,
        ?int $maxLength = null,
        ?int $minLength = null,
        ?int $maxItems = null,
        ?int $minItems = null,
        ?bool $uniqueItems = null,
        ?string $pattern = null,
        array|string|null $enum = null,
        ?Discriminator $discriminator = null,
        ?bool $readOnly = null,
        ?bool $writeOnly = null,
        ?Xml $xml = null,
        ?ExternalDocumentation $externalDocs = null,
        mixed $example = Generator::UNDEFINED,
        ?bool $nullable = null,
        ?bool $deprecated = null,
        ?array $allOf = null,
        ?array $anyOf = null,
        ?array $oneOf = null,
        AdditionalProperties|bool|null $additionalProperties = null,
        $const = Generator::UNDEFINED,
        // annotation
        ?array $x = null,
        ?array $attachables = null,
        public readonly ?string $groupsProvider = null,
    ) {
        self::$_blacklist = array_merge(self::$_blacklist, ['groupsProvider']);

        parent::__construct(
            $ref,
            $schema,
            $title,
            $description,
            $maxProperties,
            $minProperties,
            $required,
            $properties,
            $type,
            $format,
            $items,
            $collectionFormat,
            $default,
            $maximum,
            $exclusiveMaximum,
            $minimum,
            $exclusiveMinimum,
            $maxLength,
            $minLength,
            $maxItems,
            $minItems,
            $uniqueItems,
            $pattern,
            $enum,
            $discriminator,
            $readOnly,
            $writeOnly,
            $xml,
            $externalDocs,
            $example,
            $nullable,
            $deprecated,
            $allOf,
            $anyOf,
            $oneOf,
            $additionalProperties,
            $const,
            $x,
            $attachables,
        );
    }
}
