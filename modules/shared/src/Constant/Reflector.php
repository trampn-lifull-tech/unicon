<?php

namespace Chaos\Common\Constant;

/**
 * Class Reflector
 * @author ntd1712
 */
class Reflector
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
     * @return  bool
     */
    public static function has($key)
    {
        $name = self::init();

        return isset(self::$cache[$name][$key]) || in_array($key, array_flip(self::$cache[$name]), true);
    }

    /**
     * Returns all of the constants.
     *
     * @return  array
     */
    public static function values()
    {
        $name = self::init();

        return self::$cache[$name];
    }

    /**
     * Should be called after your class definition.
     *
     * @return  string
     */
    private static function init()
    {
        if (empty(self::$cache[$name = static::class])) {
            try {
                $reflection = reflect($name);
                self::$cache[$name] = array_flip($reflection->getConstants() + $reflection->getStaticProperties());
            } catch (\ReflectionException $ex) {
                self::$cache[$name] = [];
            }
        }

        return $name;
    }
}
