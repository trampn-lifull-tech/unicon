<?php

namespace Chaos\Foundation\Types;

/**
 * Class VoidType
 * @author ntd1712
 */
class VoidType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * @param   mixed $value The value to convert.
     * @return  void
     */
    public function convertToPHPValue($value)
    {
        //
    }
}
