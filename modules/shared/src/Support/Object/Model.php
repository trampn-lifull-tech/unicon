<?php

namespace Chaos\Support\Object;

/**
 * Class Model
 * @author ntd1712
 *
 * We use Model to transport Data between Layers.
 */
class Model extends \stdClass implements Contract\ModelInterface
{
    use Contract\ObjectTrait;

    /**
     * {@inheritdoc}
     *
     * @return  array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    // <editor-fold desc="Magic methods" defaultstate="collapsed">

    /**
     * @param   string $name The name of the property being interacted with.
     * @return  mixed
     * @throws  \BadMethodCallException
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        $getter = 'get' . str_replace('_', '', $name);

        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        throw new \BadMethodCallException(
            sprintf(
                '"%s" does not have a callable "%s" getter method which must be defined',
                $name,
                'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $name)))
            )
        );
    }

    /**
     * @param   string $name The name of the property being interacted with.
     * @param   mixed $value The value the $name'ed property should be set to.
     * @return  void
     * @throws  \BadMethodCallException
     */
    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;

            return;
        }

        $setter = 'set' . str_replace('_', '', $name);

        if (method_exists($this, $setter)) {
            $this->$setter($value);

            return;
        }

        throw new \BadMethodCallException(
            sprintf(
                '"%s" does not have a callable "%s" ("%s") setter method which must be defined',
                $name,
                'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $name))),
                $setter
            )
        );
    }

    /**
     * @param   string $name The name of the property being interacted with.
     * @return  bool
     */
    public function __isset($name)
    {
        try {
            return null !== $this->__get($name);
        } catch (\BadMethodCallException $ex) {
            return false;
        }
    }

    /**
     * @param   string $name The name of the property being interacted with.
     * @return  void
     * @throws  \InvalidArgumentException
     */
    public function __unset($name)
    {
        try {
            $this->__set($name, null);
        } catch (\BadMethodCallException $ex) {
            throw new \InvalidArgumentException(
                'The class property $' . $name . ' cannot be unset as NULL is an invalid value for it',
                0,
                $ex
            );
        }
    }

    // </editor-fold>
}
