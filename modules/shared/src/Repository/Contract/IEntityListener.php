<?php

namespace Chaos\Common\Repository\Contract;

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
     * @param   \Chaos\Common\Repository\Entity|\Chaos\Common\Repository\Contract\IEntity $entity The entity.
     * @param   \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs The event arguments.
     * @return  void
     * @throws  \Exception
     */
    public function postLoad($entity, $eventArgs);
}
