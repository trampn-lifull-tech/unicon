<?php

namespace Chaos\SharedModule\Support\Event;

/**
 * Class EventArgs
 * @author ntd1712
 *
 * @todo
 */
class EventArgs
{
    /**
     * @var array
     */
    private $results = [];

    /**
     * @param   string $event The event.
     * @param   mixed $result The result.
     * @return  static
     */
    public function addResult($event, $result)
    {
        $this->results[$event] = $result;

        return $this;
    }

    /**
     * @return  static
     */
    public function clearResults()
    {
        $this->results = [];

        return $this;
    }

    /**
     * @return  array
     */
    public function getResults()
    {
        return $this->results;
    }
}
