# OpenApiBundle

Provides a tight integration of the famous [`zircote/swagger-php`](https://github.com/zircote/swagger-php) library into the Symfony full-stack framework for generating 
OpenAPI documentation and building Restful APIs quickly.

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

## License

This software is published under the [MIT License](LICENSE)
