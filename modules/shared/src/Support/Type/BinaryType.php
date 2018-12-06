<?php

namespace Chaos\Foundation\Types;

use Chaos\Foundation\Exceptions\ConversionException;

/**
 * Class BinaryType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\BinaryType
 */
class BinaryType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * <code>
     * $value = Type::getType(Type::BINARY_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param   mixed $value The value to convert.
     * @return  null|resource
     */
    public function convertToPHPValue($value)
    {
        if (null === $value) {
            return $value;
        }

        if (is_string($value)) {
            $stream = fopen('php://memory', 'r+b');
            fwrite($stream, $value);
            rewind($stream);
            $value = $stream;
        }

        if (!is_resource($value)) {
            throw ConversionException::conversionFailed($value, $this);
        }

        return $value;
    }
}
