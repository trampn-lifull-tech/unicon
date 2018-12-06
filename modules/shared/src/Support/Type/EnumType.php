<?php

namespace Chaos\Foundation\Types;

use Chaos\Foundation\Exceptions\ConversionException;

/**
 * Class EnumType
 * @author ntd1712
 */
class EnumType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * <code>
     * $value = Type::getType(Type::ENUM_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param   mixed $value The value to convert.
     * @return  null|object
     * @throws  \ReflectionException
     */
    public function convertToPHPValue($value)
    {
        if (null === $value) {
            return $value;
        }

        if (is_string($value) && class_exists($value)) {
            return reflect($value)->newInstanceWithoutConstructor();
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        $val = @unserialize($value);

        if (false === $val && 'b:0;' !== $value) {
            throw ConversionException::conversionFailed($value, $this);
        }

        return $val;
    }
}
