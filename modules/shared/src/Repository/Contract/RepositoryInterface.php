<?php

namespace Chaos\Repository\Contract;

use Chaos\Support\Config\Contract\VarsAwareInterface;
use Chaos\Support\Container\Contract\ContainerAwareInterface;
use Chaos\Support\Container\Contract\InitializerInterface;
use Chaos\Support\Object\Contract\ObjectInterface;

/**
 * Interface RepositoryInterface
 * @author ntd1712
 */
interface RepositoryInterface extends ObjectInterface, InitializerInterface, ContainerAwareInterface, VarsAwareInterface
{
    //
}
