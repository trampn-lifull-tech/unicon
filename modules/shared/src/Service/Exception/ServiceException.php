<?php

namespace Chaos\Service\Exception;

/**
 * Class ServiceException
 * @author ntd1712
 */
class ServiceException extends \RuntimeException
{
    /**
     * @var string
     */
    protected $message = 'BAD_REQUEST';
    /**
     * @var mixed|int
     */
    protected $code = 400;
}
