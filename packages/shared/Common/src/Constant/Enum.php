<?php

namespace Chaos\Common\Constant;

/**
 * Class Enum
 * @author ntd1712
 */
class Enum
{
    /**
     * @var array
     */
    private static $cache = [];

    /**
     * Private constructor.
     */
    final private function __construct()
    {
        //
    }

    /**
     * Checks if such constant exists.
     *
     * <code>
     * JoinType::has('innerJoin');
     * </code>
     *
     * @param   string $key The key.
     * @return  boolean
     * @throws  \ReflectionException
     */
    public static function has($key)
    {
        $name = self::init();

        return isset(self::$cache[$name][$key]) || in_array($key, self::$cache[$name], true);
    }

    /**
     * Returns all of the constants in flip order.
     *
     * @return  array
     * @throws  \ReflectionException
     */
    public static function values()
    {
        $name = self::init();

        return array_flip(self::$cache[$name]);
    }

    /**
     * Should be called after your class definition.
     *
     * @return  string
     * @throws  \ReflectionException
     */
    private static function init()
    {
        if (empty(self::$cache[$name = get_called_class()])) {
            $reflectionClass = reflect($name);
            self::$cache[$name] = $reflectionClass->getConstants() + $reflectionClass->getStaticProperties();
        }

        return $name;
    }
}
