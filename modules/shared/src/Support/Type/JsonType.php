<?php

namespace Chaos\Foundation\Types;

use Chaos\Foundation\Exceptions\ConversionException;

/**
 * Class JsonType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\JsonType
 */
class JsonType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * <code>
     * $value = Type::getType(Type::JSON_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param   mixed $value The value to convert.
     * @return  mixed
     */
    public function convertToPHPValue($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        $val = @json_decode($value, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw ConversionException::conversionFailed($value, $this);
        }

        return $val;
    }
}
