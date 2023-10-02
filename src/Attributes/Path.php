<?php

namespace Yceruto\OpenApiBundle\Attributes;

use OpenApi\Attributes\Attachable;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\PathParameter;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\XmlContent;
use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class Path extends PathParameter
{
    public function __construct(
        ?string $parameter = null,
        ?string $name = null,
        ?string $description = null,
        ?string $in = null,
        ?bool $required = null,
        ?bool $deprecated = null,
        ?bool $allowEmptyValue = null,
        object|string|null $ref = null,
        ?Schema $schema = null,
        mixed $example = Generator::UNDEFINED,
        ?array $examples = null,
        JsonContent|array|Attachable|XmlContent|null $content = null,
        ?string $style = null,
        ?bool $explode = null,
        ?bool $allowReserved = null,
        ?array $spaceDelimited = null,
        ?array $pipeDelimited = null,
        ?array $x = null,
        ?array $attachables = null,
        public readonly string $format = Generator::UNDEFINED,
        public readonly array|string $enum = Generator::UNDEFINED,
    ) {
        parent::__construct(
            $parameter,
            $name,
            $description,
            $in,
            $required,
            $deprecated,
            $allowEmptyValue,
            $ref,
            $schema,
            $example,
            $examples,
            $content,
            $style,
            $explode,
            $allowReserved,
            $spaceDelimited,
            $pipeDelimited,
            $x,
            $attachables
        );
    }
}
