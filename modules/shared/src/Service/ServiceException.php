<?php

namespace Chaos\SharedModule\Service;

/**
 * Class ServiceException
 * @author ntd1712
 */
class ServiceException extends \RuntimeException
{
    /**
     * @var mixed|int
     */
    protected $code = 500;
}
