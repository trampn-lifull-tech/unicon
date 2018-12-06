<?php

namespace Chaos\Foundation\Types;

use Chaos\Foundation\Exceptions\ConversionException;

/**
 * Class BigIntType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\BigIntType
 */
class BigIntType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * <code>
     * $value = Type::getType(Type::BIGINT_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param   mixed $value The value to convert.
     * @return  null|string
     */
    public function convertToPHPValue($value)
    {
        if (is_array($value) || is_object($value)) {
            throw ConversionException::conversionFailed(gettype($value), $this);
        }

        return null === $value ? null : (string) $value;
    }
}
