<?php

namespace Chaos\Foundation\Types;

use Chaos\Foundation\Exceptions\ConversionException;

/**
 * Class DateImmutableType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\DateImmutableType
 */
class DateImmutableType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * <code>
     * $value = Type::getType(Type::DATE_IMMUTABLE_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param   mixed $value The value to convert.
     * @return  null|\DateTimeImmutable
     */
    public function convertToPHPValue($value)
    {
        if (null === $value || $value instanceof \DateTimeImmutable) {
            return $value;
        }

        $format = $this->__getConfig()->get('framework.date_format') ?: parent::DATE_FORMAT;
        $val = \DateTimeImmutable::createFromFormat('!' . $format . '+', $value);

        if (!$val) {
            throw ConversionException::conversionFailedFormat($value, $this, $format);
        }

        return $val;
    }
}
