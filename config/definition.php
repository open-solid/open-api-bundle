<?php

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $configurator): void {
    $configurator->rootNode()
        ->addDefaultsIfNotSet()
        ->children()
            ->arrayNode('paths')
                ->defaultValue([
                    '%kernel.project_dir%/src/',
                ])
                ->scalarPrototype()->end()
            ->end()
        ->end()
    ->end();
};
