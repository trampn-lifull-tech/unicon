<?php

namespace Chaos\Support\Contract;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Trait ContainerAware
 * @author ntd1712
 */
trait ContainerAware // implements IContainerAware
{
    // <editor-fold desc="IContainerAware implementation">

    /**
     * @JMS\Serializer\Annotation\Exclude()
     */
    private static $gbk9xbds;

    /**
     * {@inheritdoc}
     *
     * @return  ContainerBuilder|\Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return self::$gbk9xbds;
    }

    /**
     * {@inheritdoc}
     *
     * @param   array|\Symfony\Component\DependencyInjection\ContainerInterface $container Either be
     *          an array holding the paths to the service files or a <tt>ContainerBuilder</tt> instance.
     * @return  static
     */
    public function setContainer($container)
    {
        if (empty($container)) {
            $container = new ContainerBuilder;
        } else if (!$container instanceof ContainerInterface) {
            try {
                $paths = $container;
                $container = new ContainerBuilder;
                $loader = new YamlFileLoader($container, new FileLocator($paths));

                foreach ($paths as $resource) {
                    $loader->load($resource);
                }

                // https://symfony.com/doc/master/components/dependency_injection/compilation.html
                // $container->compile();
            } catch (\Exception $ex) {
                $container = new ContainerBuilder;
            }
        }

        self::$gbk9xbds = $container;

        return $this;
    }

    // </editor-fold>
}
