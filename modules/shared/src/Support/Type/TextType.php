<?php

namespace Chaos\Foundation\Types;

/**
 * Class TextType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\TextType
 */
class TextType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * <code>
     * $value = Type::getType(Type::TEXT_TYPE)->convertToPHPValue($value);
     * </code>
     *
     * @param   mixed $value The value to convert.
     * @return  string
     */
    public function convertToPHPValue($value)
    {
        return is_resource($value) ? stream_get_contents($value) : $value;
    }
}
