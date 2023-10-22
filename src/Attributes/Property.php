<?php

namespace Yceruto\OpenApiBundle\Attributes;

use OpenApi\Attributes\AdditionalProperties;
use OpenApi\Attributes\Attachable;
use OpenApi\Attributes\Discriminator;
use OpenApi\Attributes\ExternalDocumentation;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\Xml;
use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::TARGET_CLASS_CONSTANT | \Attribute::IS_REPEATABLE)]
class Property extends \OpenApi\Attributes\Property
{
    /**
     * @param string|class-string|object|null                 $ref
     * @param string[]                                        $required
     * @param \OpenApi\Attributes\Property[]                  $properties
     * @param int|float                                       $maximum
     * @param int|float                                       $minimum
     * @param string[]|int[]|float[]|\UnitEnum[]|class-string $enum
     * @param array<Schema|\OpenApi\Annotations\Schema>       $allOf
     * @param array<Schema|\OpenApi\Annotations\Schema>       $anyOf
     * @param array<Schema|\OpenApi\Annotations\Schema>       $oneOf
     * @param array<string,mixed>|null                        $x
     * @param Attachable[]|null                               $attachables
     */
    public function __construct(
        ?string $property = null,
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
        // annotation
        ?array $x = null,
        ?array $attachables = null,
        // custom properties
        public readonly ?array $groups = null,
    ) {
        self::$_blacklist = array_unique(array_merge(self::$_blacklist, ['groups']));

        parent::__construct(
            $property,
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
            $x,
            $attachables
        );
    }
}
