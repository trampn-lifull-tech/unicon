<?php

namespace Chaos\Support\Config\Contract;

/**
 * Interface IVars
 * @author ntd1712
 */
interface IVars extends \ArrayAccess
{
    /**
     * Object oriented get access for the array.
     *
     * @param   mixed $key The key to get the value for.
     * @return  null|bool|mixed The resource key value.
     */
    public function get($key);

    /**
     * Object oriented set access for the array.
     *
     * @param   mixed $key The key to set the value for.
     * @param   mixed $value The value to set.
     * @return  void
     */
    public function set($key, $value);

    /**
     * Returns the content of the resource.
     *
     * @return  array The content.
     */
    public function getContent();

    /**
     * Sets the resource contents.
     *
     * @param   array $content The content.
     * @return  self
     */
    public function setContent($content);

    /**
     * Converts the array into a flat dot notation array.
     *
     * @param   bool $flatten_array Flatten arrays into none existent keys.
     * @return  array The dot notation array.
     */
    public function toDots($flatten_array = true);

    /**
     * Makes it so the content is available in getenv()
     *
     * @return  void
     */
    public function toEnv();
}
