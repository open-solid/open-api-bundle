<?php

namespace Yceruto\OpenApiBundle\Attribute;

use OpenApi\Attributes\Attachable;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\PathParameter;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\XmlContent;
use OpenApi\Generator;

/**
 * @property string $_parameter;
 * @property string $_name;
 * @property string $_description;
 * @property string $_in;
 * @property bool $_isRequired;
 * @property bool $_deprecated;
 * @property bool $_allowEmptyValue;
 * @property object|string $_ref;
 * @property Schema $_schema;
 * @property mixed $_example;
 * @property array $_examples;
 * @property JsonContent|array|Attachable|XmlContent $_content;
 * @property string $_style;
 * @property bool $_explode;
 * @property bool $_allowReserved;
 * @property array $_spaceDelimited;
 * @property array $_pipeDelimited;
 * @property array $_x;
 * @property array $_attachables;
 * @property string $_format;
 * @property array|string $_enum;
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class Path extends PathParameter
{
    /**
     * @param array|class-string $enum
     */
    public function __construct(
        // path properties
        ?string $parameter = null,
        ?string $name = null,
        ?string $description = null,
        ?string $in = null,
        ?bool $required = null,
        ?bool $deprecated = null,
        ?bool $allowEmptyValue = null,
        object|string|null $ref = null,
        ?Schema $schema = null,
        mixed $example = null,
        ?array $examples = null,
        JsonContent|array|Attachable|XmlContent|null $content = null,
        ?string $style = null,
        ?bool $explode = null,
        ?bool $allowReserved = null,
        ?array $spaceDelimited = null,
        ?array $pipeDelimited = null,
        ?array $x = null,
        ?array $attachables = null,
        // custom properties
        public ?string $format = null,
        public array|string|null $enum = null,
    ) {
        parent::__construct(
            $parameter ?? $this->_parameter ?? null,
            $name ?? $this->_name ?? null,
            $description ?? $this->_description ?? null,
            $in ?? $this->_in ?? null,
            $required ?? $this->_isRequired ?? null,
            $deprecated ?? $this->_deprecated ?? null,
            $allowEmptyValue ?? $this->_allowEmptyValue ?? null,
            $ref ?? $this->_ref ?? null,
            $schema ?? $this->_schema ?? null,
            $example ?? $this->_example ?? Generator::UNDEFINED,
            $examples ?? $this->_examples ?? null,
            $content ?? $this->_content ?? null,
            $style ?? $this->_style ?? null,
            $explode ?? $this->_explode ?? null,
            $allowReserved ?? $this->_allowReserved ?? null,
            $spaceDelimited ?? $this->_spaceDelimited ?? null,
            $pipeDelimited ?? $this->_pipeDelimited ?? null,
            $x ?? $this->_x ?? null,
            $attachables ?? $this->_attachables ?? null,
        );
        $this->format = $format ?? $this->_format ?? Generator::UNDEFINED;
        $this->enum = $enum ?? $this->_enum ?? Generator::UNDEFINED;

        unset(
            $this->_parameter,
            $this->_name,
            $this->_description,
            $this->_in,
            $this->_isRequired,
            $this->_deprecated,
            $this->_allowEmptyValue,
            $this->_ref,
            $this->_schema,
            $this->_example,
            $this->_examples,
            $this->_content,
            $this->_style,
            $this->_explode,
            $this->_allowReserved,
            $this->_spaceDelimited,
            $this->_pipeDelimited,
            $this->_x,
            $this->_attachables,
            $this->_format,
            $this->_enum,
        );
    }

    public function __get(string $property)
    {
        if (str_starts_with($property, '_')) {
            return $this->{$property} ?? null;
        }

        return parent::__get($property);
    }
}
