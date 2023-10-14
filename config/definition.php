<?php

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $configurator): void {
    $configurator->rootNode()
        ->children()
            ->arrayNode('generator')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('scan_dirs')
                        ->defaultValue(['%kernel.project_dir%/src/'])
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('spec')
                ->defaultValue([
                    'info' => [
                        'title' => 'API Documentation',
                        'version' => '1.0.0',
                    ],
                    'servers' => [
                        ['url' => '/'],
                    ],
                ])
                ->variablePrototype()->end()
            ->end()
        ->end()
    ->end();
};
