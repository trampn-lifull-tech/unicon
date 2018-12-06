<?php

namespace Chaos\Foundation\Types;

use Chaos\Foundation\Exceptions\ConversionException;

/**
 * Class ObjectType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\ObjectType
 */
class ObjectType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * <code>
     * $value = Type::getType(Type::OBJECT_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param   mixed $value The value to convert.
     * @return  null|object
     */
    public function convertToPHPValue($value)
    {
        if (null === $value || is_object($value)) {
            return $value;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        $val = @unserialize($value);

        if (false === $val && 'b:0;' !== $value) {
            throw ConversionException::conversionFailed($value, $this);
        }

        return (object) $val;
    }
}
