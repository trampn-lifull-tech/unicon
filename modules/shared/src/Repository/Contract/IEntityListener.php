<?php

namespace Chaos\SharedModule\Repository\Contract;

/**
 * Interface IEntityListener
 * @author ntd1712
 *
 * @link http://doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#entity-listeners-resolver
 */
interface IEntityListener
{
    /**
     * The `postLoad` event.
     *
     * @param   \Chaos\SharedModule\Repository\Contract\IEntity $entity The entity.
     * @param   \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs The event arguments.
     * @return  void
     */
    public function postLoad($entity, $eventArgs);
}
