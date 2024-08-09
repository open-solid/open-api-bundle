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
use OpenApi\Generator;

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
        $defaults = static::defaults();
        $this->format = $format ?? $defaults->format ?? Generator::UNDEFINED;
        $this->enum = $enum ?? $defaults->enum ?? Generator::UNDEFINED;

        parent::__construct(
            $parameter ?? $defaults->parameter ?? null,
            $name ?? $defaults->name ?? null,
            $description ?? $defaults->description ?? null,
            $in ?? $defaults->in ?? null,
            $required ?? $defaults->isRequired ?? null,
            $deprecated ?? $defaults->deprecated ?? null,
            $allowEmptyValue ?? $defaults->allowEmptyValue ?? null,
            $ref ?? $defaults->ref ?? null,
            $schema ?? $defaults->schema ?? null,
            $example ?? $defaults->example ?? Generator::UNDEFINED,
            $examples ?? $defaults->examples ?? null,
            $content ?? $defaults->content ?? null,
            $style ?? $defaults->style ?? null,
            $explode ?? $defaults->explode ?? null,
            $allowReserved ?? $defaults->allowReserved ?? null,
            $spaceDelimited ?? $defaults->spaceDelimited ?? null,
            $pipeDelimited ?? $defaults->pipeDelimited ?? null,
            $x ?? $defaults->x ?? null,
            $attachables ?? $defaults->attachables ?? null,
        );
    }

    public static function defaults(): PathDefaults
    {
        return PathDefaults::create();
    }
}
