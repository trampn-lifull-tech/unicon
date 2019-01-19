<?php

namespace Chaos\Repository;

use Chaos\Support\Container\Contract\ContainerAware;
use Chaos\Support\Object\Model;
use Interop\Container\ContainerInterface;

/**
 * Class Entity
 * @author ntd1712
 *
 * Entity Data Model is a model that describes entities and the relationships between them.
 */
class Entity extends Model implements Contract\EntityInterface
{
    use ContainerAware;

    /**
     * {@inheritdoc}
     *
     * @param   \Interop\Container\ContainerInterface $container The container object.
     * @param   object $instance [optional]
     * @return  static
     * @throws
     */
    public function __invoke(ContainerInterface $container, $instance = null)
    {
        $this->setContainer($container);
        $this->getContainer()->set($this->getClass(), $this);

        return $this;
    }
}
