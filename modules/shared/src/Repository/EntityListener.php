<?php

namespace Chaos\Repository;

use Chaos\Support\Container\Contract\ContainerAware;

/**
 * Class EntityListener
 * @author ntd1712
 *
 * TODO
 */
abstract class EntityListener implements Contract\IEntityListener
{
    use ContainerAware;

    /**
     * {@inheritdoc}
     *
     * @param   \Chaos\Repository\Contract\IEntity $entity The entity.
     * @param   \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs The event arguments.
     * @return  void
     * @throws  \Exception
     */
    public function postLoad($entity, $eventArgs)
    {
        $entity->setContainer($this->getContainer());
        $entity->getContainer()
            ->get('config')
            ->set($entity->getClass(), $eventArgs->getEntityManager()->getUnitOfWork()->getEntityIdentifier($entity));
    }
}
