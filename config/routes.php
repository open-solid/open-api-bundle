<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Yceruto\OpenApiBundle\Controller\OpenApiController;

return static function (RoutingConfigurator $routes): void {
    $routes->add('openapi', '/')
        ->controller(OpenApiController::class)
        ->methods(['GET'])
    ;

    $routes->add('openapi_json', '/openapi.json')
        ->controller([OpenApiController::class, 'json'])
        ->methods(['GET'])
    ;

    $routes->add('openapi_json_schema', '/openapi/schema/{name}')
        ->controller([OpenApiController::class, 'jsonSchema'])
        ->methods(['GET'])
    ;
};
