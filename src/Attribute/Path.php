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

use OpenApi\Attributes\Attachable;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\PathParameter;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\XmlContent;
use OpenApi\Undefined;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class Path extends PathParameter
{
    /**
     * "format" and "enum" describe the parameter value, so they are moved to the schema.
     *
     * @see \OpenSolid\OpenApiBundle\OpenApi\Processor\AugmentPathParameters
     */
    public static $_blacklist = ['_context', '_unmerged', '_analysis', 'attachables', 'format', 'enum'];

    /**
     * @param array|class-string $enum
     */
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
        mixed $deepObject = null,
        ?array $x = null,
        ?array $attachables = null,
        // custom properties
        public ?string $format = null,
        public array|string|null $enum = null,
    ) {
        $defaults = static::defaults();
        $this->format = $format ?? $defaults->format ?? Undefined::UNDEFINED;
        $this->enum = $enum ?? $defaults->enum ?? Undefined::UNDEFINED;

        parent::__construct(
            parameter: $parameter ?? $defaults->parameter ?? null,
            name: $name ?? $defaults->name ?? null,
            description: $description ?? $defaults->description ?? Undefined::UNDEFINED,
            in: $in ?? $defaults->in ?? null,
            required: $required ?? $defaults->required ?? null,
            deprecated: $deprecated ?? $defaults->deprecated ?? null,
            allowEmptyValue: $allowEmptyValue ?? $defaults->allowEmptyValue ?? null,
            ref: $ref ?? $defaults->ref ?? null,
            schema: $schema ?? $defaults->schema ?? null,
            example: $example ?? $defaults->example ?? Undefined::UNDEFINED,
            examples: $examples ?? $defaults->examples ?? null,
            content: $content ?? $defaults->content ?? null,
            style: $style ?? $defaults->style ?? null,
            explode: $explode ?? $defaults->explode ?? null,
            allowReserved: $allowReserved ?? $defaults->allowReserved ?? null,
            spaceDelimited: $spaceDelimited ?? $defaults->spaceDelimited ?? null,
            pipeDelimited: $pipeDelimited ?? $defaults->pipeDelimited ?? null,
            deepObject: $deepObject ?? $defaults->deepObject ?? null,
            x: $x ?? $defaults->x ?? null,
            attachables: $attachables ?? $defaults->attachables ?? null,
        );
    }

    public static function defaults(): PathDefaults
    {
        return PathDefaults::create();
    }
}
