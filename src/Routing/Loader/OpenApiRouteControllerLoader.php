<?php

namespace OpenSolid\OpenApiBundle\Routing\Loader;

use Symfony\Bundle\FrameworkBundle\Routing\AttributeRouteControllerLoader;

class OpenApiRouteControllerLoader extends AttributeRouteControllerLoader
{
    public function supports(mixed $resource, string $type = null): bool
    {
        return parent::supports($resource, $type) && $this->isOpenApiController($resource);
    }

    private function isOpenApiController(mixed $resource): bool
    {
        $reflection = new \ReflectionClass($resource);

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            if ($reflectionMethod->getAttributes($this->routeAnnotationClass, \ReflectionAttribute::IS_INSTANCEOF)) {
                return true;
            }
        }

        return false;
    }
}
