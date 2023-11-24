<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use OpenSolid\OpenApiBundle\Controller\OpenApiController;

return static function (RoutingConfigurator $routes): void {
    $routes
        ->add('openapi', '/')
            ->controller([OpenApiController::class, 'index'])
            ->methods(['GET'])

        ->add('openapi_yaml', '/openapi.yaml')
            ->controller([OpenApiController::class, 'yaml'])
            ->methods(['GET'])

        ->add('openapi_json', '/openapi.json')
            ->controller([OpenApiController::class, 'json'])
            ->methods(['GET'])

        ->add('openapi_json_schema', '/schema/{name}')
            ->controller([OpenApiController::class, 'jsonSchema'])
            ->methods(['GET'])
    ;
};
