<?php

namespace Chaos\Shared\Component\Object\Contract;

/**
 * Interface IModel
 * @author ntd1712
 */
interface IModel extends IObject
{
    /**
     * Gets the properties of the model object.
     *
     * @return  array
     */
    public function toArray();
}
