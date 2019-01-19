<?php

namespace Chaos\Support\Object;

/**
 * Class Collection
 * @author ntd1712
 */
class Collection extends \ArrayObject implements Contract\CollectionInterface
{
    use Contract\CollectionTrait, Contract\ObjectTrait;
}
