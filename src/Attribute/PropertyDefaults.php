<?php

namespace Yceruto\OpenApiBundle\Attribute;

use OpenApi\Attributes\AdditionalProperties;
use OpenApi\Attributes\Discriminator;
use OpenApi\Attributes\ExternalDocumentation;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Xml;

class PropertyDefaults
{
    public string $property;
    public string|object $ref;
    public string $schema;
    public string $title;
    public string $description;
    public int $maxProperties;
    public int $minProperties;
    public array $required;
    public array $properties;
    public string $type;
    public string $format;
    public Items $items;
    public string $collectionFormat;
    public mixed $default;
    public int|float $maximum;
    public bool $exclusiveMaximum;
    public int|float $minimum;
    public bool $exclusiveMinimum;
    public int $maxLength;
    public int $minLength;
    public int $maxItems;
    public int $minItems;
    public bool $uniqueItems;
    public string $pattern;
    public array|string $enum;
    public Discriminator $discriminator;
    public bool $readOnly;
    public bool $writeOnly;
    public Xml $xml;
    public ExternalDocumentation $externalDocs;
    public mixed $example;
    public bool $nullable;
    public bool $deprecated;
    public array $allOf;
    public array $anyOf;
    public array $oneOf;
    public AdditionalProperties|bool $additionalProperties;
    public array $x;
    public array $attachables;
    public readonly array $groups;

    public static function create(): self
    {
        return new self();
    }

    public function property(string $property): self
    {
        $this->property = $property;

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

    public function type(string $type): self
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

    public function exclusiveMaximum(bool $value = true): self
    {
        $this->exclusiveMaximum = $value;

        return $this;
    }

    public function minimum(int|float $minimum): self
    {
        $this->minimum = $minimum;

        return $this;
    }

    public function exclusiveMinimum(bool $value = true): self
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

    public function pattern(string $pattern): self
    {
        $this->pattern = $pattern;

        return $this;
    }

    public function enum(array|string $enum): self
    {
        $this->enum = $enum;

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

    public function additionalProperties(AdditionalProperties|bool $additionalProperties): self
    {
        $this->additionalProperties = $additionalProperties;

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

    public function groups(array $groups): self
    {
        $this->groups = $groups;

        return $this;
    }
}
