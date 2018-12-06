<?php

namespace Chaos\Foundation\Types;

use Chaos\Foundation\Exceptions\ConversionException;

/**
 * Class VarDateTimeImmutableType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\VarDateTimeImmutableType
 */
class VarDateTimeImmutableType extends DateTimeType
{
    /**
     * {@inheritdoc} @override
     *
     * <code>
     * $value = Type::getType(Type::VARDATETIME_IMMUTABLE_TYPE)->convertToPHPValue($value);
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

        $val = date_create_immutable($value);

        if (!$val) {
            throw ConversionException::conversionFailed($value, $this);
        }

        return $val;
    }
}
