<?php

namespace Chaos\Common\Service\Contract;

/**
 * Trait EventTrait
 * @author ntd1712
 *
 * @todo
 */
trait EventTrait
{
    /**
     * Triggers a specified event.
     *
     * @param   string $event The event name.
     * @param   \Chaos\Common\Service\Event\EventArgs|array $eventArgs The event arguments.
     * @param   object $instance To trigger events in another.
     * @return  static
     * @throws  \ReflectionException
     */
    public function trigger($event, $eventArgs = null, $instance = null)
    {
        if (method_exists($instance ?: $instance = $this, $event)) {
            if (is_array($eventArgs)) {
                $eventArgs = reflect(array_shift($eventArgs))->newInstanceArgs($eventArgs);
            }

            if (null !== ($result = call_user_func([$instance, $event], $eventArgs)) && null !== $eventArgs) {
                $eventArgs->addResult($event, $result);
            }
        }

        return $this;
    }
}
