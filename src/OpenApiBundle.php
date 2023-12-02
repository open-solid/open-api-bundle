<?php

namespace OpenSolid\OpenApiBundle;

use OpenSolid\OpenApiBundle\DependencyInjection\Compiler\SerializerMappingPass;
use OpenSolid\OpenApiBundle\DependencyInjection\Compiler\TrackPathsPass;
use OpenSolid\OpenApiBundle\DependencyInjection\Compiler\ValidatorMappingPass;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class OpenApiBundle extends AbstractBundle
{
    protected string $extensionAlias = 'openapi';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('../config/definition.php');
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ValidatorMappingPass());
        $container->addCompilerPass(new SerializerMappingPass());
        $container->addCompilerPass(new TrackPathsPass(), PassConfig::TYPE_AFTER_REMOVING);
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->prependExtensionConfig('framework', [
            'exceptions' => [
                ValidationFailedException::class => ['status_code' => 422],
            ],
        ]);
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $paths = array_merge(
            [$config['default_path']],
            $config['paths'],
            [dirname(__DIR__).'/config/openapi/default.yaml'],
        );

        $container->parameters()
            ->set('openapi_paths', $paths)
        ;

        $container->import('../config/services.php');
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
