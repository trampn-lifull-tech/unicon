<?php

namespace Chaos\Foundation\Types;

/**
 * Class TrueType
 * @author ntd1712
 */
class TrueType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * @param   mixed $value The value to convert.
     * @return  boolean
     */
    public function convertToPHPValue($value)
    {
        return true;
    }
}
