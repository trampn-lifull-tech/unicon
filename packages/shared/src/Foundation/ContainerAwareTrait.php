<?php

namespace Shared\Foundation;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Trait ContainerAwareTrait
 * @author ntd1712
 */
trait ContainerAwareTrait
{
    /**
     * @JMS\Serializer\Annotation\Exclude()
     */
    private static $c5x8oh0mal;

    /**
     * Gets a reference to the container object. The object returned will be of type <tt>ContainerInterface</tt>.
     *
     * @return  ContainerBuilder|\Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return self::$c5x8oh0mal;
    }

    /**
     * Sets a reference to the container object.
     *
     * @param   array|\ArrayAccess|\Symfony\Component\DependencyInjection\ContainerInterface $container Either be
     *          an array holding the paths to the service files or a <tt>ContainerBuilder</tt> instance.
     * @return  static
     * @throws  \Exception
     */
    public function setContainer($container)
    {
        if (!$container instanceof ContainerInterface) {
            $paths = $container;
            $container = new ContainerBuilder;
            $loader = new YamlFileLoader($container, new FileLocator($paths));

            foreach ($paths as $resource) {
                $loader->load($resource);
            }

            // https://symfony.com/doc/current/components/dependency_injection/compilation.html
            // $container->compile();
        }

        self::$c5x8oh0mal = $container;

        return $this;
    }
}
