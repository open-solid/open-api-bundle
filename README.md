# OpenApiBundle

Provides a tight integration of the famous [`zircote/swagger-php`](https://github.com/zircote/swagger-php) library into the Symfony full-stack framework for generating 
OpenAPI documentation and building Restful APIs quickly.

This bundle is especially created for API-First development.

## Installation

```bash
composer require yceruto/open-api-bundle
```

Import the Swagger controller routes in `config/routes.yaml`:
```yaml
openapi:
    resource: '@OpenApiBundle/config/routes.php'
```

## Basic Usage

Create a controller with `#[Post]` and a `#[Body]` attribute in the action method:
```php
<?php

namespace App\Presentation\Controller\Post;

use App\Domain\View\ProductView;
use OpenSolid\OpenApiBundle\Attributes\Body;
use OpenSolid\OpenApiBundle\Routing\Attribute\Post;

class PostProductAction
{
    #[Post('/products')]
    public function __invoke(#[Body] PostProductBody $body): ProductView
    {
        // ...
    }
}
```

Create a payload class with a `#[Schema]` attribute and `#[Property]` attributes in the properties:
```php
<?php

namespace App\Presentation\Controller\Post;

use OpenApi\Attributes\Schema;
use OpenSolid\OpenApiBundle\Attributes\Property;

#[Schema]
class PostProductBody
{
    #[Property(minLength: 3)]
    public string $name;
}
```

Create a view class with a `#[Schema]` attribute and `#[Property]` attributes in the properties:
```php
<?php

namespace App\Domain\View;

use DateTimeImmutable;
use OpenApi\Attributes\Schema;
use OpenSolid\OpenApiBundle\Attributes\Property;

#[Schema]
readonly class ProductView
{
    #[Property(format: 'uuid')]
    public string $id;

    #[Property]
    public string $name;

    #[Property]
    public DateTimeImmutable $createdAt;
}
```

Initialize the Web server and navigate to the `https://127.0.0.1:8000/` URL to see the generated documentation.

## License

This software is published under the [MIT License](LICENSE)
