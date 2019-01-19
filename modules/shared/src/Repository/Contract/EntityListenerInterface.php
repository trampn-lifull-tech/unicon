<?php

namespace Chaos\Repository\Contract;

/**
 * Interface EntityListenerInterface
 * @author ntd1712
 *
 * @link http://doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#entity-listeners-resolver
 *
 * TODO
 */
interface EntityListenerInterface
{
    /**
     * The `postLoad` event.
     *
     * @param   \Chaos\Repository\Contract\EntityInterface $entity The entity.
     * @param   \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs The event arguments.
     * @return  void
     */
    public function postLoad($entity, $eventArgs);
}
