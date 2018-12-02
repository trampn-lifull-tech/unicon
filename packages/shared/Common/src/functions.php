<?php

if (!function_exists('isBlank')) {
    /**
     * @param   string $var Includes tab, vertical tab, line feed, carriage return and form feed characters.
     * @return  bool
     */
    function isBlank($var)
    {
        return null === $var || '' === $var || ctype_space($var);
    }
}

if (!function_exists('isJson')) {
    /**
     * <code>
     * $boolean = false !== ($decodedValue = isJson($json, false, 512, JSON_BIGINT_AS_STRING));
     * </code>
     *
     * @return  bool|mixed
     */
    function isJson()
    {
        $json = @call_user_func_array('json_decode', func_get_args());

        return JSON_ERROR_NONE === json_last_error() ? $json : false;
    }
}

if (!function_exists('guessNs')) {
    /**
     * @param   string $var The class name.
     * @param   string $prefix [optional] The prefix.
     * @return  string
     */
    function guessNs($var, $prefix = '')
    {
        if (class_exists($var, false)) {
            return $var;
        }

        $classes = preg_grep('/' . preg_quote($var) . '$/', get_declared_classes());

        return empty($classes) ? $prefix . '\\' . $var : end($classes); // use the last one if more than one
    }
}

if (!function_exists('reflect')) {
    /**
     * Constructs a ReflectionClass.
     *
     * @param   object|string $var Either a string containing the name of the class to reflect, or an object.
     * @return  ReflectionClass
     * @throws  ReflectionException
     */
    function reflect($var)
    {
        return new ReflectionClass($var);
    }
}

if (!function_exists('shorten')) {
    /**
     * Gets the unqualified class name.
     *
     * <code>
     * $unqualifiedName = shorten('A\B\C\D'); // 'D';
     * </code>
     *
     * @param   string $var The fully qualified class name.
     * @return  string
     */
    function shorten($var)
    {
        if (false !== ($string = strrchr($var, '\\'))) {
            return substr($string, 1);
        }

        return $var;
    }
}

if (!function_exists('traversableToArray')) {
    /**
     * Converts a traversable object to a common array.
     *
     * @param   \Traversable $var
     * @return  array
     */
    function traversableToArray(\Traversable $var)
    {
        if (method_exists($var, 'getArrayCopy')) {
            /** @see ArrayObject::getArrayCopy, ArrayIterator::getArrayCopy */
            $array = $var->getArrayCopy();
        } else if (method_exists($var, 'toArray')) {
            /** @see Collection::toArray */
            $array = $var->toArray();
        } else {
            $array = [];

            foreach ($var as $v) {
                $array[] = $v;
            }
        }

        return $array;
    }
}
