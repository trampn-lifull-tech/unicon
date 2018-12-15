<?php

namespace Chaos\Common\Contract;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Trait ContainerAware
 * @author ntd1712
 */
trait ContainerAware
{
    /**
     * @JMS\Serializer\Annotation\Exclude()
     */
    private static $gbk9xbds;

    /**
     * Gets a reference to the container object. The object returned will be of type <tt>ContainerInterface</tt>.
     *
     * @return  ContainerBuilder|\Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return self::$gbk9xbds;
    }

    /**
     * Sets a reference to the container object.
     *
     * <code>
     * $this->setVars([
     *     '/modules/core/src/Lookup/services.yml',
     *     '/modules/app/src/Dashboard/services.yml'
     * ]);
     * $this->setContainer([]);
     * </code>
     *
     * @param   array|\Symfony\Component\DependencyInjection\ContainerInterface $container Either be
     *          an array holding the paths to the service files or a <tt>ContainerBuilder</tt> instance.
     * @return  static
     * @throws  \Exception
     */
    public function setContainer($container)
    {
        if (empty($container)) {
            $container = new ContainerBuilder;
        } else if (!$container instanceof ContainerInterface) {
            $paths = $container;
            $container = new ContainerBuilder;
            $loader = new YamlFileLoader($container, new FileLocator($paths));

            foreach ($paths as $resource) {
                $loader->load($resource);
            }

            // https://symfony.com/doc/master/components/dependency_injection/compilation.html
            // $container->compile();
        }

        self::$gbk9xbds = $container;

        return $this;
    }
}
