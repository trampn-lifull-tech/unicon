<?php

namespace Chaos\Foundation\Types;

use Chaos\Foundation\Exceptions\ConversionException;

/**
 * Class VarDateTimeType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\VarDateTimeType
 */
class VarDateTimeType extends DateTimeType
{
    /**
     * {@inheritdoc} @override
     *
     * <code>
     * $value = Type::getType(Type::VARDATETIME_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param   mixed $value The value to convert.
     * @return  null|\DateTime
     */
    public function convertToPHPValue($value)
    {
        if (null === $value || $value instanceof \DateTime) {
            return $value;
        }

        $val = date_create($value);

        if (!$val) {
            throw ConversionException::conversionFailed($value, $this);
        }

        return $val;
    }
}
