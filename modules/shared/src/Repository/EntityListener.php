<?php

namespace Chaos\Common\Repository;

use Chaos\Common\Support\Contract\ConfigAware;
use Chaos\Common\Support\Contract\ContainerAware;

/**
 * @todo
 *
 * Class EntityListener
 * @author ntd1712
 */
abstract class EntityListener implements Contract\IEntityListener
{
    use ConfigAware, ContainerAware;

    /**
     * {@inheritdoc}
     *
     * @param   \Chaos\Common\Repository\Entity|\Chaos\Common\Repository\Contract\IEntity $entity The entity.
     * @param   \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs The event arguments.
     * @return  void
     */
    public function postLoad($entity, $eventArgs)
    {
        $entity
            ->setEntityIdentifier($eventArgs->getEntityManager()->getUnitOfWork()->getEntityIdentifier($entity))
            ->setContainer($this->getContainer())
            ->setVars($this->getVars());
    }
}
