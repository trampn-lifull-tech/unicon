<?php

namespace Chaos\Service\Exception;

/**
 * Class ValidationException
 * @author ntd1712
 */
class ValidationException extends \RuntimeException
{
    /**
     * @var mixed|int
     */
    protected $code = 422;
}
