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
use OpenApi\Attributes\Discriminator;
use OpenApi\Attributes\Encoding;
use OpenApi\Attributes\ExternalDocumentation;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Xml;

class PropertyDefaults
{
    public string $property;
    public Encoding $encoding;
    public string|object $ref;
    public string $schema;
    public string $title;
    public string $description;
    public int $maxProperties;
    public int $minProperties;
    public array $required;
    public array $properties;
    public string|array $type;
    public string $format;
    public Items $items;
    public string $collectionFormat;
    public string $pattern;
    public Discriminator $discriminator;
    public bool $readOnly;
    public bool $writeOnly;
    public Xml $xml;
    public ExternalDocumentation $externalDocs;
    public mixed $example;
    public array $examples;
    public bool $nullable;
    public bool $deprecated;
    public array $allOf;
    public array $anyOf;
    public array $oneOf;
    public string $contentEncoding;
    public string $contentMediaType;
    public mixed $default;
    public int|float $maximum;
    public bool|int|float $exclusiveMaximum;
    public int|float $minimum;
    public bool|int|float $exclusiveMinimum;
    public int $maxLength;
    public int $minLength;
    public int $maxItems;
    public int $minItems;
    public bool $uniqueItems;
    public array|string $enum;
    public mixed $not;
    public AdditionalProperties|bool $additionalProperties;
    public array $additionalItems;
    public array $contains;
    public int $minContains;
    public int $maxContains;
    public array $prefixItems;
    public array $patternProperties;
    public array $unevaluatedProperties;
    public mixed $unevaluatedItems;
    public mixed $dependencies;
    public array $dependentRequired;
    public array $dependentSchemas;
    public mixed $propertyNames;
    public mixed $const;
    public mixed $if;
    public mixed $then;
    public mixed $else;
    public mixed $contentSchema;
    public array $x;
    public array $attachables;
    public int|float $multipleOf;
    public array $groups;

    public static function create(): self
    {
        return new self();
    }

    public function property(string $property): self
    {
        $this->property = $property;

        return $this;
    }

    public function encoding(Encoding $encoding): self
    {
        $this->encoding = $encoding;

        return $this;
    }

    public function ref(string|object $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function schema(string $schema): self
    {
        $this->schema = $schema;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function maxProperties(int $maxProperties): self
    {
        $this->maxProperties = $maxProperties;

        return $this;
    }

    public function minProperties(int $minProperties): self
    {
        $this->minProperties = $minProperties;

        return $this;
    }

    public function required(array $required): self
    {
        $this->required = $required;

        return $this;
    }

    public function properties(array $properties): self
    {
        $this->properties = $properties;

        return $this;
    }

    public function type(string|array $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function format(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function items(Items $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function collectionFormat(string $collectionFormat): self
    {
        $this->collectionFormat = $collectionFormat;

        return $this;
    }

    public function pattern(string $pattern): self
    {
        $this->pattern = $pattern;

        return $this;
    }

    public function discriminator(Discriminator $discriminator): self
    {
        $this->discriminator = $discriminator;

        return $this;
    }

    public function readOnly(bool $value = true): self
    {
        $this->readOnly = $value;

        return $this;
    }

    public function writeOnly(bool $value = true): self
    {
        $this->writeOnly = $value;

        return $this;
    }

    public function xml(Xml $xml): self
    {
        $this->xml = $xml;

        return $this;
    }

    public function externalDocs(ExternalDocumentation $externalDocs): self
    {
        $this->externalDocs = $externalDocs;

        return $this;
    }

    public function example(mixed $example): self
    {
        $this->example = $example;

        return $this;
    }

    public function examples(array $examples): self
    {
        $this->examples = $examples;

        return $this;
    }

    public function nullable(bool $value = true): self
    {
        $this->nullable = $value;

        return $this;
    }

    public function deprecated(bool $value = true): self
    {
        $this->deprecated = $value;

        return $this;
    }

    public function allOf(array $allOf): self
    {
        $this->allOf = $allOf;

        return $this;
    }

    public function anyOf(array $anyOf): self
    {
        $this->anyOf = $anyOf;

        return $this;
    }

    public function oneOf(array $oneOf): self
    {
        $this->oneOf = $oneOf;

        return $this;
    }

    public function contentEncoding(string $contentEncoding): self
    {
        $this->contentEncoding = $contentEncoding;

        return $this;
    }

    public function contentMediaType(string $contentMediaType): self
    {
        $this->contentMediaType = $contentMediaType;

        return $this;
    }

    public function default(mixed $default): self
    {
        $this->default = $default;

        return $this;
    }

    public function maximum(int|float $maximum): self
    {
        $this->maximum = $maximum;

        return $this;
    }

    public function exclusiveMaximum(bool|int|float $value = true): self
    {
        $this->exclusiveMaximum = $value;

        return $this;
    }

    public function minimum(int|float $minimum): self
    {
        $this->minimum = $minimum;

        return $this;
    }

    public function exclusiveMinimum(bool|int|float $value = true): self
    {
        $this->exclusiveMinimum = $value;

        return $this;
    }

    public function maxLength(int $maxLength): self
    {
        $this->maxLength = $maxLength;

        return $this;
    }

    public function minLength(int $minLength): self
    {
        $this->minLength = $minLength;

        return $this;
    }

    public function maxItems(int $maxItems): self
    {
        $this->maxItems = $maxItems;

        return $this;
    }

    public function minItems(int $minItems): self
    {
        $this->minItems = $minItems;

        return $this;
    }

    public function uniqueItems(bool $value = true): self
    {
        $this->uniqueItems = $value;

        return $this;
    }

    public function enum(array|string $enum): self
    {
        $this->enum = $enum;

        return $this;
    }

    public function not(mixed $not): self
    {
        $this->not = $not;

        return $this;
    }

    public function additionalProperties(AdditionalProperties|bool $additionalProperties): self
    {
        $this->additionalProperties = $additionalProperties;

        return $this;
    }

    public function additionalItems(array $additionalItems): self
    {
        $this->additionalItems = $additionalItems;

        return $this;
    }

    public function contains(array $contains): self
    {
        $this->contains = $contains;

        return $this;
    }

    public function minContains(int $minContains): self
    {
        $this->minContains = $minContains;

        return $this;
    }

    public function maxContains(int $maxContains): self
    {
        $this->maxContains = $maxContains;

        return $this;
    }

    public function prefixItems(array $prefixItems): self
    {
        $this->prefixItems = $prefixItems;

        return $this;
    }

    public function patternProperties(array $patternProperties): self
    {
        $this->patternProperties = $patternProperties;

        return $this;
    }

    public function unevaluatedProperties(array $unevaluatedProperties): self
    {
        $this->unevaluatedProperties = $unevaluatedProperties;

        return $this;
    }

    public function unevaluatedItems(mixed $unevaluatedItems): self
    {
        $this->unevaluatedItems = $unevaluatedItems;

        return $this;
    }

    public function dependencies(mixed $dependencies): self
    {
        $this->dependencies = $dependencies;

        return $this;
    }

    public function dependentRequired(array $dependentRequired): self
    {
        $this->dependentRequired = $dependentRequired;

        return $this;
    }

    public function dependentSchemas(array $dependentSchemas): self
    {
        $this->dependentSchemas = $dependentSchemas;

        return $this;
    }

    public function propertyNames(mixed $propertyNames): self
    {
        $this->propertyNames = $propertyNames;

        return $this;
    }

    public function const(mixed $const): self
    {
        $this->const = $const;

        return $this;
    }

    public function if(mixed $if): self
    {
        $this->if = $if;

        return $this;
    }

    public function then(mixed $then): self
    {
        $this->then = $then;

        return $this;
    }

    public function else(mixed $else): self
    {
        $this->else = $else;

        return $this;
    }

    public function contentSchema(mixed $contentSchema): self
    {
        $this->contentSchema = $contentSchema;

        return $this;
    }

    public function x(array $x): self
    {
        $this->x = $x;

        return $this;
    }

    public function attachables(array $attachables): self
    {
        $this->attachables = $attachables;

        return $this;
    }

    public function multipleOf(int|float $multipleOf): self
    {
        $this->multipleOf = $multipleOf;

        return $this;
    }

    public function groups(array $groups): self
    {
        $this->groups = $groups;

        return $this;
    }
}
