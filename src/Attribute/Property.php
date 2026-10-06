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

namespace OpenSolid\OpenApiBundle\Attribute;

use OpenApi\Attributes\AdditionalProperties;
use OpenApi\Attributes\Attachable;
use OpenApi\Attributes\Discriminator;
use OpenApi\Attributes\Encoding;
use OpenApi\Attributes\ExternalDocumentation;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\Xml;
use OpenApi\Undefined;

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
     * @param string[]|null                                   $groups
     */
    public function __construct(
        ?string $property = null,
        ?Encoding $encoding = null,
        string|object|null $ref = null,
        ?string $schema = null,
        ?string $title = null,
        ?string $description = null,
        ?int $maxProperties = null,
        ?int $minProperties = null,
        ?array $required = null,
        ?array $properties = null,
        string|array|null $type = null,
        ?string $format = null,
        ?Items $items = null,
        ?string $collectionFormat = null,
        ?string $pattern = null,
        ?Discriminator $discriminator = null,
        ?bool $readOnly = null,
        ?bool $writeOnly = null,
        ?Xml $xml = null,
        ?ExternalDocumentation $externalDocs = null,
        mixed $example = null,
        ?array $examples = null,
        ?bool $nullable = null,
        ?bool $deprecated = null,
        ?array $allOf = null,
        ?array $anyOf = null,
        ?array $oneOf = null,
        ?string $contentEncoding = null,
        ?string $contentMediaType = null,
        mixed $default = null,
        int|float|null $maximum = null,
        bool|int|float|null $exclusiveMaximum = null,
        int|float|null $minimum = null,
        bool|int|float|null $exclusiveMinimum = null,
        ?int $maxLength = null,
        ?int $minLength = null,
        ?int $maxItems = null,
        ?int $minItems = null,
        ?bool $uniqueItems = null,
        array|string|null $enum = null,
        mixed $not = null,
        AdditionalProperties|bool|null $additionalProperties = null,
        ?array $additionalItems = null,
        ?array $contains = null,
        ?int $minContains = null,
        ?int $maxContains = null,
        ?array $prefixItems = null,
        ?array $patternProperties = null,
        ?array $unevaluatedProperties = null,
        mixed $unevaluatedItems = null,
        mixed $dependencies = null,
        ?array $dependentRequired = null,
        ?array $dependentSchemas = null,
        mixed $propertyNames = null,
        mixed $const = null,
        mixed $if = null,
        mixed $then = null,
        mixed $else = null,
        mixed $contentSchema = null,
        ?array $x = null,
        ?array $attachables = null,
        int|float|null $multipleOf = null,
        // custom properties
        public ?array $groups = null,
    ) {
        if (!\in_array('groups', self::$_blacklist, true)) {
            self::$_blacklist[] = 'groups';
        }

        $defaults = static::defaults();
        $this->groups = $groups ?? $defaults->groups ?? null;

        parent::__construct(
            property: $property ?? $defaults->property ?? null,
            encoding: $encoding ?? $defaults->encoding ?? null,
            ref: $ref ?? $defaults->ref ?? null,
            schema: $schema ?? $defaults->schema ?? null,
            title: $title ?? $defaults->title ?? null,
            description: $description ?? $defaults->description ?? Undefined::UNDEFINED,
            maxProperties: $maxProperties ?? $defaults->maxProperties ?? null,
            minProperties: $minProperties ?? $defaults->minProperties ?? null,
            required: $required ?? $defaults->required ?? null,
            properties: $properties ?? $defaults->properties ?? null,
            type: $type ?? $defaults->type ?? null,
            format: $format ?? $defaults->format ?? null,
            items: $items ?? $defaults->items ?? null,
            collectionFormat: $collectionFormat ?? $defaults->collectionFormat ?? null,
            pattern: $pattern ?? $defaults->pattern ?? null,
            discriminator: $discriminator ?? $defaults->discriminator ?? null,
            readOnly: $readOnly ?? $defaults->readOnly ?? null,
            writeOnly: $writeOnly ?? $defaults->writeOnly ?? null,
            xml: $xml ?? $defaults->xml ?? null,
            externalDocs: $externalDocs ?? $defaults->externalDocs ?? null,
            example: $example ?? $defaults->example ?? Undefined::UNDEFINED,
            examples: $examples ?? $defaults->examples ?? null,
            nullable: $nullable ?? $defaults->nullable ?? null,
            deprecated: $deprecated ?? $defaults->deprecated ?? null,
            allOf: $allOf ?? $defaults->allOf ?? null,
            anyOf: $anyOf ?? $defaults->anyOf ?? null,
            oneOf: $oneOf ?? $defaults->oneOf ?? null,
            contentEncoding: $contentEncoding ?? $defaults->contentEncoding ?? null,
            contentMediaType: $contentMediaType ?? $defaults->contentMediaType ?? null,
            default: $default ?? $defaults->default ?? Undefined::UNDEFINED,
            maximum: $maximum ?? $defaults->maximum ?? null,
            exclusiveMaximum: $exclusiveMaximum ?? $defaults->exclusiveMaximum ?? null,
            minimum: $minimum ?? $defaults->minimum ?? null,
            exclusiveMinimum: $exclusiveMinimum ?? $defaults->exclusiveMinimum ?? null,
            maxLength: $maxLength ?? $defaults->maxLength ?? null,
            minLength: $minLength ?? $defaults->minLength ?? null,
            maxItems: $maxItems ?? $defaults->maxItems ?? null,
            minItems: $minItems ?? $defaults->minItems ?? null,
            uniqueItems: $uniqueItems ?? $defaults->uniqueItems ?? null,
            enum: $enum ?? $defaults->enum ?? null,
            not: $not ?? $defaults->not ?? Undefined::UNDEFINED,
            additionalProperties: $additionalProperties ?? $defaults->additionalProperties ?? null,
            additionalItems: $additionalItems ?? $defaults->additionalItems ?? null,
            contains: $contains ?? $defaults->contains ?? null,
            minContains: $minContains ?? $defaults->minContains ?? null,
            maxContains: $maxContains ?? $defaults->maxContains ?? null,
            prefixItems: $prefixItems ?? $defaults->prefixItems ?? null,
            patternProperties: $patternProperties ?? $defaults->patternProperties ?? null,
            unevaluatedProperties: $unevaluatedProperties ?? $defaults->unevaluatedProperties ?? null,
            unevaluatedItems: $unevaluatedItems ?? $defaults->unevaluatedItems ?? Undefined::UNDEFINED,
            dependencies: $dependencies ?? $defaults->dependencies ?? Undefined::UNDEFINED,
            dependentRequired: $dependentRequired ?? $defaults->dependentRequired ?? null,
            dependentSchemas: $dependentSchemas ?? $defaults->dependentSchemas ?? null,
            propertyNames: $propertyNames ?? $defaults->propertyNames ?? Undefined::UNDEFINED,
            const: $const ?? $defaults->const ?? Undefined::UNDEFINED,
            if: $if ?? $defaults->if ?? Undefined::UNDEFINED,
            then: $then ?? $defaults->then ?? Undefined::UNDEFINED,
            else: $else ?? $defaults->else ?? Undefined::UNDEFINED,
            contentSchema: $contentSchema ?? $defaults->contentSchema ?? Undefined::UNDEFINED,
            x: $x ?? $defaults->x ?? null,
            attachables: $attachables ?? $defaults->attachables ?? null,
        );

        if (null !== $multipleOf ??= $defaults->multipleOf ?? null) {
            $this->multipleOf = $multipleOf;
        }
    }

    public static function defaults(): PropertyDefaults
    {
        return PropertyDefaults::create();
    }
}
