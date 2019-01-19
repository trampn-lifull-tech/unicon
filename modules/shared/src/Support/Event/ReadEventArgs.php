<?php

namespace Chaos\Support\Event;

/**
 * Class ReadEventArgs
 * @author ntd1712
 *
 * TODO
 */
class ReadEventArgs extends EventArgs
{
    /**
     * @var mixed
     */
    private $criteria;
    /**
     * @var mixed
     */
    private $data;

    /**
     * Constructor.
     *
     * @param   mixed $criteria The criteria.
     * @param   mixed $data [optional] The data.
     */
    public function __construct($criteria, $data = null)
    {
        $this->criteria = $criteria;
        $this->data = $data;
    }

    /**
     * @return  mixed|string|int
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param   mixed $criteria The criteria.
     * @return  self
     */
    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * @return  mixed|\Traversable
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param   mixed $data The data.
     * @return  self
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
