<?php

namespace Chaos\Foundation\Types;

use Chaos\Foundation\Exceptions\ConversionException;

/**
 * Class DateIntervalType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\DateIntervalType
 */
class DateIntervalType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * <code>
     * $value = Type::getType(Type::DATEINTERVAL_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param   mixed $value The value to convert.
     * @return  null|\DateInterval
     */
    public function convertToPHPValue($value)
    {
        if (null === $value || $value instanceof \DateInterval) {
            return $value;
        }

        try {
            return new \DateInterval($value);
        } catch (\Exception $exception) {
            throw ConversionException::conversionFailedFormat($value, $this, 'PY-m-dTH:i:s');
        }
    }
}
