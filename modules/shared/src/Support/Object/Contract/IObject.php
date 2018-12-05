<?php

namespace Chaos\Common\Support\Object\Contract;

/**
 * Interface IObject
 * @author ntd1712
 */
interface IObject
{
    /**
     * Indicates whether other object is "equal to" this one.
     *
     * @param   \Chaos\Common\Support\Object\Contract\IObject $other The reference object with which to compare.
     * @return  bool
     */
    public function equals(IObject $other);

    /**
     * Returns the runtime class of this object.
     *
     * @return  string
     */
    public function getClass();

    /**
     * Returns a hash code value for the object.
     *
     * @return  string
     */
    public function getHashCode();
}
