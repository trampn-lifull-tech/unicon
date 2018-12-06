<?php

namespace Chaos\Foundation\Types;

use Chaos\Foundation\Exceptions\ConversionException;

/**
 * Class ArrayType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\ArrayType
 */
class ArrayType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * <code>
     * $value = Type::getType(Type::ARRAY_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param   mixed $value The value to convert.
     * @return  null|array
     */
    public function convertToPHPValue($value)
    {
        if (null === $value || is_array($value)) {
            return $value;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        if (version_compare(PHP_VERSION, '7.0.0', '>=')) {
            $val = @unserialize($value, ['allowed_classes' => false]);
        } else {
            $val = @unserialize($value);
        }

        if (false === $val && 'b:0;' !== $value) {
            throw ConversionException::conversionFailed($value, $this);
        }

        return (array) $val;
    }
}
