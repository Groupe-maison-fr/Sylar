<?php

declare(strict_types=1);

namespace App;

use App\Infrastructure\CompilerPass\ConsoleCommandFilterCompilerPass;
use App\Infrastructure\PostContainerDumpActions\PostContainerDumpServiceInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getProjectDir(): string
    {
        return __DIR__ . '/../';
    }

    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    protected function dumpContainer(ConfigCache $cache, ContainerBuilder $container, string $class, string $baseClass): void
    {
        parent::dumpContainer($cache, $container, $class, $baseClass);
        // $this->postDumpContainerAction($container);
    }

    public function build(ContainerBuilder $container): void
    {
        if ($this->environment === 'prod') {
            $container->addCompilerPass(new ConsoleCommandFilterCompilerPass());
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getProjectDir() . '/config/bundles.php'));
        $container->setParameter('container.dumper.inline_class_loader', \PHP_VERSION_ID < 70400 || $this->debug);
        $container->setParameter('container.dumper.inline_factories', true);
        $confDir = realpath($this->getProjectDir() . '/config');

        $loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{packages}/' . $this->environment . '/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/services/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/services/' . $this->environment . '/*' . self::CONFIG_EXTS, 'glob');
    }

    private function postDumpContainerAction(ContainerBuilder $container): void
    {
        /** @var PostContainerDumpServiceInterface $postContainerDumpAction */
        $postContainerDumpAction = $container->get(PostContainerDumpServiceInterface::class);
        $postContainerDumpAction->execute();
    }
}
