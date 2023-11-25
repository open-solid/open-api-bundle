<?php

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $configurator): void {
    $configurator->rootNode()
        ->addDefaultsIfNotSet()
        ->children()
            ->scalarNode('default_path')
                ->defaultValue('%kernel.project_dir%/src/')
            ->end()
            ->arrayNode('paths')
                ->defaultValue([])
                ->scalarPrototype()->end()
            ->end()
        ->end()
    ->end();
};
