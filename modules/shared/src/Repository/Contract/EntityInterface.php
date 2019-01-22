<?php

namespace Chaos\Repository\Contract;

use Chaos\Support\Container\Contract\ContainerAwareInterface;
use Chaos\Support\Container\Contract\InitializerInterface;
use Chaos\Support\Object\Contract\ModelInterface;

/**
 * Interface EntityInterface
 * @author ntd1712
 */
interface EntityInterface extends ModelInterface, InitializerInterface, ContainerAwareInterface
{
    //
}
