<?php

namespace Shared\Component\Object;

/**
 * Class Collection
 * @author ntd1712
 */
abstract class Collection extends \ArrayObject implements Contract\ICollection
{
    use CollectionAware, ObjectAware;
}
