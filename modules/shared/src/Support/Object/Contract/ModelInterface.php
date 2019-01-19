<?php

namespace Chaos\Support\Object\Contract;

/**
 * Interface ModelInterface
 * @author ntd1712
 */
interface ModelInterface extends ObjectInterface
{
    /**
     * Gets the properties of the model object.
     *
     * @return  array
     */
    public function toArray();
}
