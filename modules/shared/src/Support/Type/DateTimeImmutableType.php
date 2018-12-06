<?php

namespace Chaos\Foundation\Types;

use Chaos\Foundation\Exceptions\ConversionException;

/**
 * Class DateTimeImmutableType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\DateTimeImmutableType
 */
class DateTimeImmutableType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * <code>
     * $value = Type::getType(Type::DATETIME_IMMUTABLE_TYPE)->convertToPHPValue($value);
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

        $format = $this->__getConfig()->get('framework.datetime_format') ?: parent::DATETIME_FORMAT;
        $val = \DateTimeImmutable::createFromFormat($format . '+', $value);

        if (!$val) {
            $val = date_create_immutable($value);
        }

        if (!$val) {
            throw ConversionException::conversionFailedFormat($value, $this, $format);
        }

        return $val;
    }
}
