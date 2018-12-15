<?php

namespace Chaos\Common\Object\Contract;

/**
 * Trait CollectionTrait
 * @author ntd1712
 *
 * @property-read array $storage
 */
trait CollectionTrait
{
    // <editor-fold desc="ICollection implementation">

    /**
     * {@inheritdoc}
     *
     * @param   mixed $element The element to add.
     * @return  bool Always TRUE.
     */
    public function add($element)
    {
        $this->storage[] = $element;

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return  void
     */
    public function clear()
    {
        $this->storage = [];
    }

    /**
     * {@inheritdoc}
     *
     * @param   mixed $element The element to search for.
     * @param   bool $strict [optional]
     * @return  bool TRUE if the collection contains the element, FALSE otherwise.
     */
    public function contains($element, $strict = true)
    {
        return in_array($element, $this->storage, $strict);
    }

    /**
     * {@inheritdoc}
     *
     * @return  bool TRUE if the collection is empty, FALSE otherwise.
     */
    public function isEmpty()
    {
        return empty($this->storage);
    }

    /**
     * {@inheritdoc}
     *
     * @param   string|int $key The kex/index of the element to remove.
     * @return  mixed The removed element or NULL, if the collection did not contain the element.
     */
    public function remove($key)
    {
        if (isset($this->storage[$key])) {
            $removed = $this->storage[$key];
            unset($this->storage[$key]);

            return $removed;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @param   mixed $element The element to remove.
     * @return  bool TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeElement($element)
    {
        $offset = $this->indexOf($element);

        if (false !== $offset) {
            unset($this->storage[$offset]);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @param   string|int $key The key/index to check for.
     * @return  bool TRUE if the collection contains an element with the specified key/index, FALSE otherwise.
     */
    public function containsKey($key)
    {
        return isset($this->storage[$key]);
    }

    /**
     * {@inheritdoc}
     *
     * @param   string|int $key The key/index of the element to retrieve.
     * @return  mixed
     */
    public function get($key)
    {
        return $this->storage[$key] ?? null;
    }

    /**
     * {@inheritdoc}
     *
     * @return  array The keys/indices of the collection, in the order of the corresponding elements in the collection.
     */
    public function getKeys()
    {
        return array_keys($this->storage);
    }

    /**
     * {@inheritdoc}
     *
     * @return  array The values of all elements in the collection, in the order they appear in the collection.
     */
    public function getValues()
    {
        return array_values($this->storage);
    }

    /**
     * {@inheritdoc}
     *
     * @param   string|int $key The key/index of the element to set.
     * @param   mixed $value The element to set.
     * @return  void
     */
    public function set($key, $value)
    {
        $this->storage[$key] = $value;
    }

    /**
     * {@inheritdoc}
     *
     * @return  array
     */
    public function toArray()
    {
        return $this->storage;
    }

    /**
     * {@inheritdoc}
     *
     * @return  mixed
     */
    public function first()
    {
        return reset($this->storage);
    }

    /**
     * {@inheritdoc}
     *
     * @return  mixed
     */
    public function last()
    {
        return end($this->storage);
    }

    /**
     * {@inheritdoc}
     *
     * @return  int|string
     */
    public function key()
    {
        return key($this->storage);
    }

    /**
     * {@inheritdoc}
     *
     * @return  mixed
     */
    public function current()
    {
        return current($this->storage);
    }

    /**
     * {@inheritdoc}
     *
     * @return  mixed
     */
    public function next()
    {
        return next($this->storage);
    }

    /**
     * {@inheritdoc}
     *
     * @param   \Closure $p The predicate.
     * @return  bool TRUE if the predicate is TRUE for at least one element, FALSE otherwise.
     */
    public function exists(\Closure $p)
    {
        foreach ($this->storage as $key => $element) {
            if ($p($key, $element)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @param   \Closure $p The predicate used for filtering.
     * @return  self A collection with the results of the filter operation.
     */
    public function filter(\Closure $p)
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return new static(array_filter($this->storage, $p));
    }

    /**
     * {@inheritdoc}
     *
     * @param   \Closure $p The predicate.
     * @return  bool TRUE, if the predicate yields TRUE for all elements, FALSE otherwise.
     */
    public function forAll(\Closure $p)
    {
        foreach ($this->storage as $key => $element) {
            if (!$p($key, $element)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @param   \Closure $func
     * @return  self
     */
    public function map(\Closure $func)
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return new static(array_map($func, $this->storage));
    }

    /**
     * {@inheritdoc}
     *
     * @param   \Closure $p The predicate on which to partition.
     * @return  self[] An array with two elements. The first element contains the collection
     *                 of elements where the predicate returned TRUE, the second element
     *                 contains the collection of elements where the predicate returned FALSE.
     */
    public function partition(\Closure $p)
    {
        $matches = $noMatches = [];

        foreach ($this->storage as $key => $element) {
            if ($p($key, $element)) {
                $matches[$key] = $element;
            } else {
                $noMatches[$key] = $element;
            }
        }

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return [new static($matches), new static($noMatches)];
    }

    /**
     * {@inheritdoc}
     *
     * @param   mixed $element The element to search for.
     * @param   bool $strict [optional]
     * @return  int|string|bool The key/index of the element or FALSE if the element was not found.
     */
    public function indexOf($element, $strict = true)
    {
        return array_search($element, $this->storage, $strict);
    }

    /**
     * {@inheritdoc}
     *
     * @param   int $offset The offset to start from.
     * @param   null|int $length [optional] The maximum number of elements to return, or null for no limit.
     * @param   bool $preserveKeys [optional]
     * @return  array
     */
    public function slice($offset, $length = null, $preserveKeys = true)
    {
        return array_slice($this->storage, $offset, $length, $preserveKeys);
    }

    // </editor-fold>
}
