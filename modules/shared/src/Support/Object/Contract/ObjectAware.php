<?php

namespace Chaos\SharedModule\Support\Object\Contract;

/**
 * Trait ObjectAware
 * @author ntd1712
 */
trait ObjectAware
{
    /**
     * Returns a string consisting of the name of the class of which the object is an instance,
     * the at-sign character `@', and the unsigned hexadecimal representation of the hash code of the object.
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->getClass() . '@' . bin2hex($this->getHashCode());
    }

    // <editor-fold desc="IObject implementation">

    /**
     * {@inheritdoc}
     *
     * @param   \Chaos\SharedModule\Support\Object\Contract\IObject $other The reference object with which to compare.
     * @return  bool
     */
    public function equals(IObject $other)
    {
        return $this === $other;
    }

    /**
     * {@inheritdoc}
     *
     * @return  string
     */
    public function getClass()
    {
        return static::class;
    }

    /**
     * {@inheritdoc}
     *
     * @return  string
     */
    public function getHashCode()
    {
        return spl_object_hash($this);
    }

    // </editor-fold>
}
