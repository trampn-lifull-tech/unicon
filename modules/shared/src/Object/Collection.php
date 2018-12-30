<?php

namespace Chaos\Object;

/**
 * Class Collection
 * @author ntd1712
 */
class Collection extends \ArrayObject implements Contract\ICollection
{
    use Contract\CollectionTrait, Contract\ObjectTrait;
}
