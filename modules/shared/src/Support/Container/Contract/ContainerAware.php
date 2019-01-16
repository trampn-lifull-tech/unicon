<?php

namespace Chaos\Support\Container\Contract;

use Chaos\Support\Container\Container;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Trait ContainerAware
 * @author ntd1712
 */
trait ContainerAware // implements IContainerAware
{
    /**
     * @JMS\Serializer\Annotation\Exclude()
     */
    private static $gbk9xbds;

    /**
     * {@inheritdoc}
     *
     * @return  Container|\Chaos\Support\Container\Contract\IContainer
     */
    public function getContainer()
    {
        return self::$gbk9xbds;
    }

    /**
     * {@inheritdoc}
     *
     * @param   object|array $container Either be an array holding the paths to the resource files
     *          or a <tt>IContainer</tt> instance.
     * @return  static
     */
    public function setContainer($container)
    {
        if (empty($container)) {
            $container = new Container;
        } else if (!$container instanceof IContainer) {
            try {
                $paths = $container;
                $container = new Container;
                $loader = new YamlFileLoader($container, new FileLocator($paths));

                foreach ($paths as $resource) {
                    $loader->load($resource);
                }

                 $container->compile();
            } catch (\Exception $ex) {
                $container = new Container;
            }
        }

        self::$gbk9xbds = $container;

        return $this;
    }
}
