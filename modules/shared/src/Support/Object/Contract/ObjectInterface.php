<?php

namespace Chaos\Support\Object\Contract;

/**
 * Interface ObjectInterface
 * @author ntd1712
 */
interface ObjectInterface
{
    /**
     * Indicates whether other object is "equal to" this one.
     *
     * @param   \Chaos\Support\Object\Contract\ObjectInterface $other The reference object with which to compare.
     * @return  bool
     */
    public function equals(ObjectInterface $other);

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
