<?php

namespace Chaos\Service\Exception;

/**
 * Class ServiceException
 * @author ntd1712
 */
class ServiceException extends \Exception
{
    /**
     * @var mixed|int
     */
    protected $code = 500;
}
