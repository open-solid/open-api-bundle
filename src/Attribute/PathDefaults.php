<?php

namespace OpenSolid\OpenApiBundle\Attribute;

use OpenApi\Attributes\Attachable;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\XmlContent;

class PathDefaults
{
    public string $parameter;
    public string $name;
    public string $description;
    public string $in;
    public bool $required;
    public bool $deprecated;
    public bool $allowEmptyValue;
    public object|string $ref;
    public Schema $schema;
    public mixed $example;
    public array $examples;
    public JsonContent|array|Attachable|XmlContent $content;
    public string $style;
    public bool $explode;
    public bool $allowReserved;
    public array $spaceDelimited;
    public array $pipeDelimited;
    public array $x;
    public array $attachables;
    public string $format;
    public array|string $enum;

    public static function create(): self
    {
        return new self();
    }

    public function parameter(string $parameter): self
    {
        $this->parameter = $parameter;

        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function in(string $in): self
    {
        $this->in = $in;

        return $this;
    }

    public function required(bool $value = true): self
    {
        $this->required = $value;

        return $this;
    }

    public function deprecated(bool $value = true): self
    {
        $this->deprecated = $value;

        return $this;
    }

    public function allowEmptyValue(bool $value = true): self
    {
        $this->allowEmptyValue = $value;

        return $this;
    }

    public function ref(object|string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function schema(Schema $schema): self
    {
        $this->schema = $schema;

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

    public function content(JsonContent|array|Attachable|XmlContent $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function style(string $style): self
    {
        $this->style = $style;

        return $this;
    }

    public function explode(bool $value = true): self
    {
        $this->explode = $value;

        return $this;
    }

    public function allowReserved(bool $value = true): self
    {
        $this->allowReserved = $value;

        return $this;
    }

    public function spaceDelimited(array $spaceDelimited): self
    {
        $this->spaceDelimited = $spaceDelimited;

        return $this;
    }

    public function pipeDelimited(array $pipeDelimited): self
    {
        $this->pipeDelimited = $pipeDelimited;

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

    public function format(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function enum(array|string $enum): self
    {
        $this->enum = $enum;

        return $this;
    }

    private function __construct()
    {
    }
}
