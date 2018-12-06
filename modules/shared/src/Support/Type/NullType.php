<?php

namespace Chaos\Foundation\Types;

/**
 * Class NullType
 * @author ntd1712
 */
class NullType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * @param   mixed $value The value to convert.
     * @return  null
     */
    public function convertToPHPValue($value)
    {
        return null;
    }
}
