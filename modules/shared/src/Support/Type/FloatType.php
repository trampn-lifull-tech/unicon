<?php

namespace Chaos\Foundation\Types;

use Chaos\Foundation\Exceptions\ConversionException;

/**
 * Class FloatType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\FloatType
 */
class FloatType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * <code>
     * $value = Type::getType(Type::FLOAT_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param   mixed $value The value to convert.
     * @return  null|float
     */
    public function convertToPHPValue($value)
    {
        if (is_object($value)) {
            throw ConversionException::conversionFailed(gettype($value), $this);
        }

        return null === $value ? null : (float) $value;
    }
}
