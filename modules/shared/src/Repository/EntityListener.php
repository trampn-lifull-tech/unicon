<?php

namespace Chaos\Common\Repository;

use Chaos\Common\Support\Contract\ConfigAware;
use Chaos\Common\Support\Contract\ContainerAware;

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
     * @param   \Chaos\Common\Repository\Entity|\Chaos\Common\Repository\Contract\IEntity $entity The entity.
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
