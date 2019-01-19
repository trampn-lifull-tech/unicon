<?php

namespace Chaos\Support\Container\Contract;

/**
 * Interface ContainerAwareInterface
 * @author ntd1712
 */
interface ContainerAwareInterface
{
    /**
     * Gets a reference to the container object. The object returned will be of type <tt>ContainerInterface</tt>.
     *
     * @return  \Chaos\Support\Container\Contract\ContainerInterface
     */
    public function getContainer();

    /**
     * Sets a reference to the container object.
     *
     * <code>
     * $this->setContainer([
     *     '/modules/core/src/Lookup/services.yml',
     *     '/modules/app/Dashboard/services.yml'
     * ]);
     * $this->setContainer([]);
     * </code>
     *
     * @param   object|array $container Either be an array holding the paths to the resource files
     *          or a <tt>ContainerInterface</tt> instance.
     * @return  static
     */
    public function setContainer($container);
}
