<?php

namespace Chaos\Foundation\Types;

use Chaos\Foundation\Exceptions\ConversionException;

/**
 * Class IntegerType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\IntegerType
 */
class IntegerType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * <code>
     * $value = Type::getType(Type::INTEGER_TYPE)->convertToPHPValue($value);
     * $value = Type::getType(Type::INT_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param   mixed $value The value to convert.
     * @return  null|integer
     */
    public function convertToPHPValue($value)
    {
        if (is_object($value)) {
            throw ConversionException::conversionFailed(gettype($value), $this);
        }

        return null === $value ? null : (int) $value;
    }
}
