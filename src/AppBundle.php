<?php

namespace App;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ResolveClassPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @final
 */
class AppBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $this->registerCompilerPass($container);
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $compilerPasses = require dirname(__DIR__).'/config/compiler_passes.php';

        foreach ($compilerPasses as $namespace => $passes) {
            foreach ($passes as $pass) {
                if (!is_object($pass)) {
                    $rootPath = $container->getParameter('kernel.project_dir').'/src/'.$namespace;
                    $pass = $this->getAnonymousCompilerPass($pass, $rootPath);
                }

                $container->addCompilerPass($pass);
            }
        }

        $container->addCompilerPass(new ResolveClassPass());
    }

    private function getAnonymousCompilerPass(string $module, string $rootPath): CompilerPassInterface
    {
        return new class("$rootPath/$module/Config/services.yaml") implements CompilerPassInterface {
            private string $moduleConfigPath;

            public function __construct(string $moduleConfigPath)
            {
                $this->moduleConfigPath = $moduleConfigPath;
            }

            public function process(ContainerBuilder $container): void
            {
                $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
                $loader->load($this->moduleConfigPath);
            }
        };
    }
}
