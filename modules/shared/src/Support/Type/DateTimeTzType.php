<?php

namespace Chaos\Foundation\Types;

use Chaos\Foundation\Exceptions\ConversionException;

/**
 * Class DateTimeTzType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\DateTimeTzType
 */
class DateTimeTzType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * <code>
     * $value = Type::getType(Type::DATETIMETZ_TYPE)->convertToPHPValue($value);
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

        $format = $this->__getConfig()->get('framework.datetimetz_format') ?: DATE_RFC1123;
        $val = \DateTime::createFromFormat($format . '+', $value);

        if (!$val) {
            throw ConversionException::conversionFailedFormat($value, $this, $format);
        }

        return $val;
    }
}
