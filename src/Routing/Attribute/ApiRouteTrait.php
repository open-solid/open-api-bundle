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

namespace OpenSolid\OpenApiBundle\Routing\Attribute;

use OpenApi\Attributes\ExternalDocumentation;
use OpenApi\Attributes\RequestBody;
use Symfony\Component\Routing\Attribute\Route;

trait ApiRouteTrait
{
    public readonly Route $route;

    public function __construct(
        // OpenAPI Path properties
        string $path,
        ?string $description = null,
        ?string $summary = null,
        ?array $security = null,
        ?array $servers = null,
        ?RequestBody $requestBody = null,
        ?array $tags = null,
        ?array $parameters = null,
        ?array $responses = null,
        ?array $callbacks = null,
        ?ExternalDocumentation $externalDocs = null,
        ?bool $deprecated = null,
        ?array $x = null,
        ?array $attachables = null,
        // Symfony Route properties
        ?string $name = null,
        array $requirements = [],
        array $options = [],
        array $defaults = [],
        ?string $host = null,
        array|string $schemes = [],
        ?string $condition = null,
        ?int $priority = null,
        ?string $locale = null,
        ?string $format = null,
        ?bool $utf8 = null,
        ?bool $stateless = null,
        ?string $env = null,
        // custom properties
        public ?string $itemsType = null,
        public ?string $when = null,
    ) {
        self::$_blacklist = array_unique(array_merge(self::$_blacklist, ['route', 'itemsType', 'when']));

        parent::__construct(
            $path,
            $name,
            $description,
            $summary,
            $security,
            $servers,
            $requestBody,
            $tags,
            $parameters,
            $responses,
            $callbacks,
            $externalDocs,
            $deprecated,
            $x,
            $attachables,
        );

        if ($condition && $when) {
            $condition .= ' and '.$when;
        } else {
            $condition = $when;
        }

        $this->route = new Route(
            $path,
            $name,
            $requirements,
            $options,
            $defaults,
            $host,
            $this->getMethod(),
            $schemes,
            $condition,
            $priority,
            $locale,
            $format,
            $utf8,
            $stateless,
            $env,
        );
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->route->{$name}(...$arguments);
    }

    abstract public function getMethod(): string;
}
