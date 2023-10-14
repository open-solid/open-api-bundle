# OpenApiBundle

Provides a tight integration of `zircote/swagger-php` library into the Symfony full-stack framework for generating 
OpenAPI documentation.

## Installation

```bash
composer require yceruto/open-api-bundle
```

## Basic Usage

Create a payload class with a `#[Schema]` attribute and `#[Property]` attributes in the properties:
```php
<?php

namespace App\Controller;

use OpenApi\Attributes\Schema;
use Yceruto\OpenApiBundle\Attributes\Property;

#[Schema]
class PostProductPayload
{
    #[Property(format: 'uuid')]
    public string $id;

    #[Property(minLength: 3)]
    public string $name;
}
```

Create a view class with a `#[Schema]` attribute and `#[Property]` attributes in the properties:
```php
<?php

namespace App\Model;

use DateTimeImmutable;
use OpenApi\Attributes\Schema;
use Yceruto\OpenApiBundle\Attributes\Property;

#[Schema]
readonly class ProductView
{
    #[Property(format: 'uuid')]
    public string $id;

    #[Property]
    public string $name;

    #[Property]
    public DateTimeImmutable $createdAt;

    #[Property]
    public ?DateTimeImmutable $updatedAt;

    // ...
}
```

Create a controller with `#[Post]` and a `#[Payload]` attribute in the action method:
```php
<?php

namespace App\Controller;

use App\Model\ProductView;
use Yceruto\OpenApiBundle\Attributes\Payload;
use Yceruto\OpenApiBundle\Routing\Attribute\Post;

class PostProductAction
{
    #[Post('/products')]
    public function __invoke(#[Payload] PostProductPayload $payload): ProductView
    {
        // ...

        return $product;
    }
}
```

Output:

![Output](cover.png)

## License

This software is published under the [MIT License](LICENSE)
