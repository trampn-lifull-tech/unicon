<?php

namespace Chaos\Support\Config\Contract;

/**
 * Interface VarsInterface
 * @author ntd1712
 */
interface VarsInterface extends \ArrayAccess
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
     * Makes it so the content is available in getenv()
     *
     * @return  void
     */
    public function toEnv();
}
