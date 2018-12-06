<?php

namespace Chaos\Foundation\Types;

use Chaos\Foundation\Exceptions\ConversionException;

/**
 * Class TimeType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\TimeType
 */
class TimeType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * <code>
     * $value = Type::getType(Type::TIME_TYPE)->convertToPHPValue($value);
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

        $format = $this->__getConfig()->get('framework.time_format') ?: parent::TIME_FORMAT;
        $val = \DateTime::createFromFormat('!' . $format . '+', $value);

        if (!$val) {
            throw ConversionException::conversionFailedFormat($value, $this, $format);
        }

        return $val;
    }
}
