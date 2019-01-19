<?php

namespace Chaos\Repository\Contract;

use Chaos\Support\Container\Contract\ContainerAwareInterface;
use Chaos\Support\Object\Contract\ModelInterface;
use Chaos\Support\Container\Contract\InitializerInterface;

/**
 * Interface EntityInterface
 * @author ntd1712
 */
interface EntityInterface extends ModelInterface, ContainerAwareInterface, InitializerInterface
{
    //
}
