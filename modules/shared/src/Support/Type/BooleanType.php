<?php

namespace Chaos\Foundation\Types;

/**
 * Class BooleanType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\BooleanType
 */
class BooleanType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * <code>
     * $value = Type::getType(Type::BOOLEAN_TYPE)->convertToPHPValue($value);
     * $value = Type::getType(Type::BOOL_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param   mixed $value The value to convert.
     * @return  null|boolean
     */
    public function convertToPHPValue($value)
    {
        if (null === $value || is_bool($value)) {
            return $value;
        }

        if (is_string($value)) { // we only have to check if something is false
            static $literals = ['0', 'f', 'false', '(false)', 'n', 'no', 'off'];

            if (in_array(strtolower($value), $literals, true)) {
                return false;
            }
        }

        return (bool) $value;
    }
}
