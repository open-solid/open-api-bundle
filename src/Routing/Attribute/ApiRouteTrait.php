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
use OpenApi\Undefined;
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
        public ?int $statusCode = null,
    ) {
        self::$_blacklist = array_unique(array_merge(self::$_blacklist, ['route', 'itemsType', 'when', 'statusCode']));

        parent::__construct(
            path: $path,
            operationId: $name,
            description: $description ?? Undefined::UNDEFINED,
            summary: $summary ?? Undefined::UNDEFINED,
            security: $security,
            servers: $servers,
            requestBody: $requestBody,
            tags: $tags,
            parameters: $parameters,
            responses: $responses,
            callbacks: $callbacks,
            externalDocs: $externalDocs,
            deprecated: $deprecated,
            x: $x,
            attachables: $attachables,
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

    public function __get(string $name): mixed
    {
        if (property_exists($this->route, $name)) {
            return $this->route->{$name};
        }

        $this->_context->logger->warning(\sprintf('Property "%s" doesn\'t exist in a %s', $name, $this->identity()));

        return null;
    }

    abstract public function getMethod(): string;
}
