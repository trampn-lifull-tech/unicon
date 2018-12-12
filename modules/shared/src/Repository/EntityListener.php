<?php

namespace Chaos\SharedModule\Repository;

use Chaos\SharedModule\Support\Contract\ConfigAware;
use Chaos\SharedModule\Support\Contract\ContainerAware;

/**
 * Class EntityListener
 * @author ntd1712
 */
abstract class EntityListener implements Contract\IEntityListener
{
    use ConfigAware, ContainerAware;

    /**
     * {@inheritdoc}
     *
     * @param   \Chaos\SharedModule\Repository\Entity|\Chaos\SharedModule\Repository\Contract\IEntity $entity The entity.
     * @param   \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs The event arguments.
     * @return  void
     * @throws  \Exception
     */
    public function postLoad($entity, $eventArgs)
    {
        $entity->setContainer($this->getContainer());
        $entity->getContainer()->get(VARS)
            ->set($entity->getClass(), $eventArgs->getEntityManager()->getUnitOfWork()->getEntityIdentifier($entity));
    }
}
