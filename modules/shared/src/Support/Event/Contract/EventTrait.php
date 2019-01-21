<?php

namespace Chaos\Support\Event\Contract;

/**
 * Trait EventTrait
 * @author ntd1712
 *
 * TODO
 */
trait EventTrait
{
    /**
     * Triggers a specified event.
     *
     * @param   string $event The event name.
     * @param   \Chaos\Support\Event\EventArgs|array $eventArgs The event arguments.
     * @param   object $instance To trigger events in another.
     * @return  static
     */
    public function trigger($event, $eventArgs = null, $instance = null)
    {
        if (method_exists($instance ?: $instance = $this, $event)) {
            if (is_array($eventArgs)) {
                try {
                    $eventArgs = reflect(array_shift($eventArgs))->newInstanceArgs($eventArgs);
                } catch (\ReflectionException $e) {
                    //
                }
            }

            if (null !== ($result = call_user_func([$instance, $event], $eventArgs)) && null !== $eventArgs) {
                $eventArgs->addResult($event, $result);
            }
        }

        return $this;
    }
}
