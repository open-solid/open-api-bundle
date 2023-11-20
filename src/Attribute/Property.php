<?php

namespace Yceruto\OpenApiBundle\Attribute;

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
        mixed $default = null,
        float|int|null $maximum = null,
        ?bool $exclusiveMaximum = null,
        float|int|null $minimum = null,
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
        mixed $example = null,
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
        public ?array $groups = null,
    ) {
        if (!\in_array('groups', self::$_blacklist, true)) {
            self::$_blacklist[] = 'groups';
        }

        $defaults = static::defaults();
        $this->groups = $groups ?? $defaults->groups ?? null;

        parent::__construct(
            $property ?? $defaults->property ?? null,
            $ref ?? $defaults->ref ?? null,
            $schema ?? $defaults->schema ?? null,
            $title ?? $defaults->title ?? null,
            $description ?? $defaults->description ?? null,
            $maxProperties ?? $defaults->maxProperties ?? null,
            $minProperties ?? $defaults->minProperties ?? null,
            $required ?? $defaults->required ?? null,
            $properties ?? $defaults->properties ?? null,
            $type ?? $defaults->type ?? null,
            $format ?? $defaults->format ?? null,
            $items ?? $defaults->items ?? null,
            $collectionFormat ?? $defaults->collectionFormat ?? null,
            $default ?? $defaults->default ?? Generator::UNDEFINED,
            $maximum ?? $defaults->maximum ?? null,
            $exclusiveMaximum ?? $defaults->exclusiveMaximum ?? null,
            $minimum ?? $defaults->minimum ?? null,
            $exclusiveMinimum ?? $defaults->exclusiveMinimum ?? null,
            $maxLength ?? $defaults->maxLength ?? null,
            $minLength ?? $defaults->minLength ?? null,
            $maxItems ?? $defaults->maxItems ?? null,
            $minItems ?? $defaults->minItems ?? null,
            $uniqueItems ?? $defaults->uniqueItems ?? null,
            $pattern ?? $defaults->pattern ?? null,
            $enum ?? $defaults->enum ?? null,
            $discriminator ?? $defaults->discriminator ?? null,
            $readOnly ?? $defaults->readOnly ?? null,
            $writeOnly ?? $defaults->writeOnly ?? null,
            $xml ?? $defaults->xml ?? null,
            $externalDocs ?? $defaults->externalDocs ?? null,
            $example ?? $defaults->example ?? Generator::UNDEFINED,
            $nullable ?? $defaults->nullable ?? null,
            $deprecated ?? $defaults->deprecated ?? null,
            $allOf ?? $defaults->allOf ?? null,
            $anyOf ?? $defaults->anyOf ?? null,
            $oneOf ?? $defaults->oneOf ?? null,
            $additionalProperties ?? $defaults->additionalProperties ?? null,
            $x ?? $defaults->x ?? null,
            $attachables ?? $defaults->attachables ?? null,
        );
    }

    public static function defaults(): PropertyDefaults
    {
        return PropertyDefaults::create();
    }
}
