<?php

namespace Chaos\Common\Service\Exception;

/**
 * Class ValidateException
 * @author ntd1712
 */
class ValidateException extends \Exception
{
    /**
     * @var mixed|int
     */
    protected $code = 418;
}
