<?php

namespace OpenSolid\OpenApiBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use OpenSolid\OpenApiBundle\DependencyInjection\Compiler\SerializerMappingPass;
use OpenSolid\OpenApiBundle\DependencyInjection\Compiler\ValidatorMappingPass;

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
        $scanDirs = $config['generator']['scan_dirs'];
        $scanDirs[] = __DIR__.'/OpenApi/Responses';

        $container->parameters()
            ->set('openapi_generator_scan_dirs', $scanDirs)
            ->set('openapi_spec', $config['spec'])
        ;

        $container->import('../config/services.php');
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
