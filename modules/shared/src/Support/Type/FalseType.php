<?php

namespace Chaos\Foundation\Types;

/**
 * Class FalseType
 * @author ntd1712
 */
class FalseType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * @param   mixed $value The value to convert.
     * @return  boolean
     */
    public function convertToPHPValue($value)
    {
        return false;
    }
}
