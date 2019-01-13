<?php

namespace Chaos\Support\Contract;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Trait InitializerAware
 * @author ntd1712
 */
trait InitializerAware // implements \Zend\ServiceManager\Initializer\InitializerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param   \Symfony\Component\DependencyInjection\ContainerInterface $container The container object.
     * @param   object $instance [optional]
     * @return  static
     */
    public function __invoke(ContainerInterface $container, $instance = null)
    {
        $container->set($this->getClass(), $this);
        $this->setContainer($container);
        $this->setVars($instance ?? $container->get(M1_VARS));

        return $this;
    }
}
