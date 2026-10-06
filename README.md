# OpenApiBundle

Provides a tight integration of the famous [`zircote/swagger-php`](https://github.com/zircote/swagger-php) library into the Symfony full-stack framework for generating 
OpenAPI spec and building Restful APIs quickly.

This bundle is especially created for API-First development.

## Installation

```bash
composer require open-solid/open-api-bundle
```

Import the bundle's routes in `config/routes.yaml` to show the Swagger API documentation:
```yaml
openapi:
    resource: '@OpenApiBundle/config/routes.php'
```

## Basic Sample

Define your OpenAPI spec and endpoint at the same time:

```php
<?php

namespace Api\Catalog\Controller\Post;

use Api\Catalog\Model\Product;
use OpenSolid\OpenApiBundle\Attribute\Payload;
use OpenSolid\OpenApiBundle\Routing\Attribute\Post;

class PostProductAction
{
    #[Post('/products')]
    public function __invoke(#[Payload] PostProductPayload $payload): Product
    {
        return new Product($payload->name, $payload->price);
    }
}
```

## Main Features

- [x] Generate OpenAPI spec from PHP attributes (based on `zircote/swagger-php` v6)
  - Automatic `Operation`, `Schema` and `Property` guessing from PHP classes and methods
- [x] Expose Swagger UI to explore the OpenAPI spec and test API endpoints
- [x] Export OpenAPI spec in JSON or YAML format (via HTTP and console command)
- [x] Import OpenAPI spec in JSON or YAML format (via config file)
- [x] Define Symfony routes and OpenAPI Paths using the same attributes:
  - `#[Get]`, `#[Post]`, `#[Put]`, `#[Patch]`, `#[Delete]`, `#[Head]`, `#[Options]`
  - One controller class can declare many of them
- [x] Conditional OpenAPI Path/Route definition:
  - Example: `#[Get('/me', when: 'service("toggle_me").isEnabled()')]`
- [x] Native Symfony mapping attributes:
  - `#[MapRequestPayload]` (including `type: Item::class` for arrays) becomes the request body
  - `#[MapQueryString]` (including `key: 'filter'`) and `#[MapQueryParameter]` become query parameters
- [x] Symfony attributes abbreviations:
  - `#[Payload]` instead of `#[MapRequestPayload]`
  - `#[Query]` instead of `#[MapQueryString]`
- [x] OpenAPI attributes abbreviations:
  - `#[Path]` instead of `#[PathParameter]`
  - `#[Param]` instead of `#[QueryParameter]`
- [x] Query and path parameter inference from PHP reflection:
  - `type`, `format`, `items`, `enum`, `default`, `required` and the array `style`
  - `description` from the property or method docblock
- [x] Schema inference from PHP reflection:
  - `required` from nullability, default values and the `NotNull` / `NotBlank` constraints
  - `type` and `enum` from backed and pure PHP enums
  - `default` from promoted constructor parameters
- [x] Symfony Validator constraints describe the spec:
  - `Length`, `Count`, `Range`, comparisons, `Regex`, `Choice`, `All`, formats (`Uuid`, `Email`, `Url`, ...) and more
- [x] OpenAPI attributes define Symfony Validator constraints:
  - Example: `#[Property(minLength: 3, maxLength: 255)]`
- [x] Response inference from the controller return type:
  - Item types of arrays from `@return list<View>` (or the `itemsType` option)
  - Declared `responses` are merged with the inferred success response
  - `401` and `403` responses for operations with `security`
  - Custom success status code with `statusCode: 202`, for the spec and the response
- [x] Automatic controller response serialization (JSON format by default)
- [ ] Generate new endpoints from API spec (WIP) (based on `open-solid/open-api-assistant-bundle`)

## Upgrade to swagger-php v6

- The bundle requires `zircote/swagger-php` ^6.12 and `symfony/type-info`.
- Docblock annotations (`@OA\...`) are not read anymore. Use PHP attributes.
- Custom processors implement `OpenSolid\OpenApiBundle\OpenApi\Processor\ProcessorInterface`.
  They are tagged `openapi.processor` automatically. The `OpenApi\Processors\ProcessorInterface`
  of swagger-php does not exist anymore.
- The `format` and `enum` options of `#[Path]` now go into the parameter `schema`.
- Object schemas now declare their `required` properties.
- Query parameters now declare their `schema`, with the `default` value instead of an `example`.
- An empty array result now returns `200` with `[]`, not `204`.

## License

This software is published under the [MIT License](LICENSE)
