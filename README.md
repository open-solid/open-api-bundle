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
    public function __invoke(#[Payload] PostProductPayload $Payload): Product
    {
        return new Product($body->name, $Payload->price);
    }
}
```

## Main Features

- [x] Generate OpenAPI spec from PHP attributes (Mainly based on `zircote/swagger-php`)
  - Automatic `Operation`, `Schema` and `Property` guessing from PHP classes and methods
- [x] Expose Swagger UI to explore the OpenAPI spec and test API endpoints
- [x] Export OpenAPI spec in JSON or YAML format (via HTTP and console command)
- [x] Import OpenAPI spec in JSON or YAML format (via config file)
- [x] Define Symfony routes and OpenAPI Paths using the same attributes:
  - `#[Post]`, `#[Get]`, `#[Put]`, `#[Patch]`, `#[Delete]`
- [x] Conditional OpenAPI Path/Route definition:
  - Example: `#[Get('/me', when: 'service("toggle_me").isEnabled()')]`
- [x] Symfony attributes abbreviations:
  - `#[Payload]` instead of `#[MapRequestPayload]`
  - `#[Query]` instead of `#[MapQueryString]`
- [x] OpenAPI attributes abbreviations:
  - `#[Path]` instead of `#[PathParameter]`
  - `#[Param]` instead of `#[QueryParameter]`
- [x] Symfony's validation constraints definition using OpenAPI attributes:
  - Example: `#[Property(minLength: 3, maxLength: 255)]`
- [x] Automatic controller response serialization (JSON format by default)
- [ ] Generate new endpoints from API spec (WIP) (based on `open-solid/open-api-assistant-bundle`)

## License

This software is published under the [MIT License](LICENSE)
