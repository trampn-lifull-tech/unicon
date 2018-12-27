<?php

namespace Chaos\Common\Contract;

/**
 * Interface IContainerAware
 * @author ntd1712
 */
interface IContainerAware
{
    /**
     * Gets a reference to the container object. The object returned will be of type <tt>ContainerInterface</tt>.
     *
     * @return  \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer();

    /**
     * Sets a reference to the container object.
     *
     * <code>
     * $this->setContainer([
     *     '/modules/core/src/Lookup/services.yml',
     *     '/modules/app/src/Dashboard/services.yml'
     * ]);
     * $this->setContainer([]);
     * </code>
     *
     * @param   array|\Symfony\Component\DependencyInjection\ContainerInterface $container Either be
     *          an array holding the paths to the service files or a <tt>ContainerBuilder</tt> instance.
     * @return  static
     */
    public function setContainer($container);
}
