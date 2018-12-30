<?php

namespace Chaos\Infrastructure\Type;

/**
 * Class Type
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\Type
 */
class Type
{
//    use ConfigAwareTrait;

    // <editor-fold desc="PREDEFINED CONSTANTS" defaultstate="collapsed">

    // Common types
    const ARRAY_TYPE = 'array';
    const BOOLEAN_TYPE = 'boolean';
    const BOOL_TYPE = 'bool';
    const CALLABLE_TYPE = 'callable';
    const CALLBACK_TYPE = 'callback';
    const CLOSURE_TYPE = 'closure';
    const DOUBLE_TYPE = 'double';
    const FALSE_TYPE = 'false';
    const FLOAT_TYPE = 'float';
    const INTEGER_TYPE = 'integer';
    const INT_TYPE = 'int';
    const LONG_TYPE = 'long';
    const MIXED_TYPE = 'mixed';
    const NULL_TYPE = 'null';
    const OBJECT_TYPE = 'object';
    const RESOURCE_TYPE = 'resource';
    const SELF_TYPE = 'self';
    const STATIC_TYPE = 'static';
    const STRING_TYPE = 'string';
    const TRUE_TYPE = 'true';
    const VOID_TYPE = 'void';
    const UNKNOWN_TYPE = 'unknown';
    // Doctrine types
    const BIGINT_TYPE = 'bigint';
    const BINARY_TYPE = 'binary';
    const BLOB_TYPE = 'blob';
    const DATE_IMMUTABLE_TYPE = 'date_immutable';
    const DATEINTERVAL_TYPE = 'dateinterval';
    const DATETIME_IMMUTABLE_TYPE = 'datetime_immutable';
    const DATETIME_TYPE = 'datetime';
    const DATETIMETZ_IMMUTABLE_TYPE = 'datetimetz_immutable';
    const DATETIMETZ_TYPE = 'datetimetz';
    const DATE_TYPE = 'date';
    const DECIMAL_TYPE = 'decimal';
    const GUID_TYPE = 'guid';
    const JSON_ARRAY_TYPE = 'json_array';
    const JSON_TYPE = 'json';
    const SIMPLE_ARRAY_TYPE = 'simple_array';
    const SMALLINT_TYPE = 'smallint';
    const TEXT_TYPE = 'text';
    const TIME_IMMUTABLE_TYPE = 'time_immutable';
    const TIME_TYPE = 'time';
    const VARDATETIME_IMMUTABLE_TYPE = 'vardatetime_immutable';
    const VARDATETIME_TYPE = 'vardatetime';
    // Custom Doctrine types
    const ENUM_TYPE = 'enum';
    const MEDIUMINT_TYPE = 'mediumint';
    const REAL_TYPE = 'real';
    const TIMESTAMP_TYPE = 'timestamp';
    const TINYINT_TYPE = 'tinyint';
    const UUID_TYPE = 'uuid';

    // Formats
    const DATETIME_FORMAT = 'Y-m-d\TH:i:s.u';
    const DATE_FORMAT = 'Y-m-d';
    const TIME_FORMAT = 'H:i:s';

    // </editor-fold>

    /**
     * @var array The map of supported types.
     */
    private static $typesMap = [
        self::ARRAY_TYPE => 'Chaos\Foundation\Types\ArrayType',
        self::BIGINT_TYPE => 'Chaos\Foundation\Types\BigIntType',
        self::BINARY_TYPE => 'Chaos\Foundation\Types\BinaryType',
        self::BLOB_TYPE => 'Chaos\Foundation\Types\BlobType',
        self::BOOLEAN_TYPE => 'Chaos\Foundation\Types\BooleanType',
        self::BOOL_TYPE => 'Chaos\Foundation\Types\BooleanType',
        self::CALLABLE_TYPE => 'Chaos\Foundation\Types\CallableType',
        self::CALLBACK_TYPE => 'Chaos\Foundation\Types\CallbackType',
        self::CLOSURE_TYPE => 'Chaos\Foundation\Types\ClosureType',
        self::DATE_IMMUTABLE_TYPE => 'Chaos\Foundation\Types\DateImmutableType',
        self::DATEINTERVAL_TYPE => 'Chaos\Foundation\Types\DateIntervalType',
        self::DATETIME_IMMUTABLE_TYPE => 'Chaos\Foundation\Types\DateTimeImmutableType',
        self::DATETIME_TYPE => 'Chaos\Foundation\Types\DateTimeType',
        self::DATETIMETZ_IMMUTABLE_TYPE => 'Chaos\Foundation\Types\DateTimeTzImmutableType',
        self::DATETIMETZ_TYPE => 'Chaos\Foundation\Types\DateTimeTzType',
        self::DATE_TYPE => 'Chaos\Foundation\Types\DateType',
        self::DECIMAL_TYPE => 'Chaos\Foundation\Types\DecimalType',
        self::DOUBLE_TYPE => 'Chaos\Foundation\Types\FloatType',
        self::ENUM_TYPE => 'Chaos\Foundation\Types\EnumType',
        self::FALSE_TYPE => 'Chaos\Foundation\Types\FalseType',
        self::FLOAT_TYPE => 'Chaos\Foundation\Types\FloatType',
        self::GUID_TYPE => 'Chaos\Foundation\Types\GuidType',
        self::INTEGER_TYPE => 'Chaos\Foundation\Types\IntegerType',
        self::INT_TYPE => 'Chaos\Foundation\Types\IntegerType',
        self::JSON_ARRAY_TYPE => 'Chaos\Foundation\Types\JsonArrayType',
        self::JSON_TYPE => 'Chaos\Foundation\Types\JsonType',
        self::LONG_TYPE => 'Chaos\Foundation\Types\IntegerType',
        self::MEDIUMINT_TYPE => 'Chaos\Foundation\Types\MediumIntType',
        self::MIXED_TYPE => 'Chaos\Foundation\Types\MixedType',
        self::NULL_TYPE => 'Chaos\Foundation\Types\NullType',
        self::OBJECT_TYPE => 'Chaos\Foundation\Types\ObjectType',
        self::REAL_TYPE => 'Chaos\Foundation\Types\FloatType',
        self::RESOURCE_TYPE => 'Chaos\Foundation\Types\ResourceType',
        self::SELF_TYPE => 'Chaos\Foundation\Types\SelfType',
        self::SIMPLE_ARRAY_TYPE => 'Chaos\Foundation\Types\SimpleArrayType',
        self::SMALLINT_TYPE => 'Chaos\Foundation\Types\SmallIntType',
        self::STATIC_TYPE => 'Chaos\Foundation\Types\StaticType',
        self::STRING_TYPE => 'Chaos\Foundation\Types\StringType',
        self::TEXT_TYPE => 'Chaos\Foundation\Types\TextType',
        self::TIMESTAMP_TYPE => 'Chaos\Foundation\Types\TimestampType',
        self::TIME_IMMUTABLE_TYPE => 'Chaos\Foundation\Types\TimeImmutableType',
        self::TIME_TYPE => 'Chaos\Foundation\Types\TimeType',
        self::TINYINT_TYPE => 'Chaos\Foundation\Types\TinyIntType',
        self::TRUE_TYPE => 'Chaos\Foundation\Types\TrueType',
        self::UNKNOWN_TYPE => 'Chaos\Foundation\Types\MixedType',
        self::UUID_TYPE => 'Chaos\Foundation\Types\GuidType',
        self::VARDATETIME_IMMUTABLE_TYPE => 'Chaos\Foundation\Types\VarDateTimeImmutableType',
        self::VARDATETIME_TYPE => 'Chaos\Foundation\Types\VarDateTimeType',
        self::VOID_TYPE => 'Chaos\Foundation\Types\VoidType'
    ];
    /**
     * @var array The map of already instantiated type objects.
     */
    private static $typeObjects = [];

    /**
     * Private constructor: to prevent instantiation and force use of the factory method.
     */
    final private function __construct()
    {
        //
    }

    /**
     * Converts a value from its database representation to its PHP representation of this type.
     *
     * @param   mixed $value The value to convert.
     * @return  mixed The PHP representation of the value.
     * @throws  \Chaos\Foundation\Exceptions\ConversionException
     */
    public function convertToPHPValue($value)
    {
        return $value;
    }

    /**
     * Factory method to create type instances. Type instances are implemented as flyweights.
     *
     * @param   string $name The name of the type.
     * @return  static
     * @throws  \Chaos\Foundation\Exceptions\ConversionException
     */
    public static function getType($name)
    {
        if (!isset(self::$typeObjects[$name])) {
            if (!isset(self::$typesMap[$name])) {
                throw ConversionException::unknownColumnType($name);
            }

            self::$typeObjects[$name] = new self::$typesMap[$name];
        }

        return self::$typeObjects[$name];
    }

    /**
     * Gets the types array map which holds all registered types and the corresponding type class.
     *
     * @return  array
     */
    public static function getTypesMap()
    {
        return self::$typesMap;
    }

    /**
     * Checks if exists support for a type.
     *
     * @param   string $name The name of the type.
     * @return  boolean TRUE if type is supported, FALSE otherwise.
     */
    public static function hasType($name)
    {
        return isset(self::$typesMap[$name]) || in_array($name, self::$typesMap, true);
    }

    // <editor-fold desc="MAGIC METHODS" defaultstate="collapsed">

    /**
     * @return  string
     */
    public function __toString()
    {
        return get_called_class();
    }

    // </editor-fold>
}
