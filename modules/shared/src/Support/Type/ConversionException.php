<?php

namespace Chaos\Common\Support\Type;

/**
 * Class ConversionException
 *
 * @see \Doctrine\DBAL\Types\ConversionException
 */
class ConversionException extends \Exception
{
    /**
     * Thrown when a type conversion fails.
     *
     * @param   string $value The value.
     * @param   string $toType The type to be converted to.
     * @return  self
     */
    public static function conversionFailed($value, $toType)
    {
        $value = 32 < strlen($value) ? substr($value, 0, 20) . '...' : $value;

        return new self('Could not convert value "' . $value . '" to ' . $toType);
    }

    /**
     * Thrown when a type conversion fails and we can make a statement about the expected format.
     *
     * @param   string $value The value.
     * @param   string $toType The type to be converted to.
     * @param   string $expectedFormat The expected format.
     * @param   null|\Exception $previous The previous Exception instance.
     * @return  self
     */
    public static function conversionFailedFormat($value, $toType, $expectedFormat, \Exception $previous = null)
    {
        $value = 32 < strlen($value) ? substr($value, 0, 20) . '...' : $value;

        return new self(
            'Could not convert value "' . $value . '" to ' . $toType
            . '. Expected format: ' . $expectedFormat, 0, $previous
        );
    }

    /**
     * Thrown when the PHP value passed to the converter was not of the expected type.
     *
     * @param   mixed $value
     * @param   string $toType
     * @param   string[] $possibleTypes
     * @return  self
     */
    public static function conversionFailedInvalidType($value, $toType, array $possibleTypes)
    {
        $actualType = is_object($value) ? get_class($value) : gettype($value);

        if (is_scalar($value)) {
            return new self(sprintf(
                "Could not convert PHP value '%s' of type '%s' to type '%s'. Expected one of the following types: %s",
                $value,
                $actualType,
                $toType,
                implode(', ', $possibleTypes)
            ));
        }

        return new self(sprintf(
            "Could not convert PHP value of type '%s' to type '%s'. Expected one of the following types: %s",
            $actualType,
            $toType,
            implode(', ', $possibleTypes)
        ));
    }

    /**
     * @param   mixed $value
     * @param   string $format
     * @param   string $error
     * @return  self
     */
    public static function conversionFailedSerialization($value, $format, $error)
    {
        $actualType = is_object($value) ? get_class($value) : gettype($value);

        return new self(sprintf(
            "Could not convert PHP type '%s' to '%s', as an '%s' error was triggered by the serialization",
            $actualType,
            $format,
            $error
        ));
    }

    /**
     * @param   string $format
     * @param   string $error
     * @return  self
     */
    public static function conversionFailedUnserialization(string $format, string $error)
    {
        return new self(sprintf(
            "Could not convert database value to '%s' as an error was triggered by the unserialization: '%s'",
            $format,
            $error
        ));
    }
}
