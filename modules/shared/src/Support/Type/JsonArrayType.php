<?php

namespace Chaos\Foundation\Types;

use Chaos\Foundation\Exceptions\ConversionException;

/**
 * Class JsonArrayType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\JsonArrayType
 */
class JsonArrayType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * <code>
     * $value = Type::getType(Type::JSON_ARRAY_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param   mixed $value The value to convert.
     * @return  array
     */
    public function convertToPHPValue($value)
    {
        if (null === $value || '' === $value) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_object($value)) {
            return (array) $value;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        $val = @json_decode($value, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw ConversionException::conversionFailed($value, $this);
        }

        return (array) $val;
    }
}
