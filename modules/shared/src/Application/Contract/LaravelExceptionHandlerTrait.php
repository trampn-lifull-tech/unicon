<?php

namespace Chaos\Application\Contract;

use Chaos\Service\Exception\ServiceException;
use Chaos\Service\Exception\ValidationException;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Encryption\DecryptException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Trait LaravelExceptionHandlerTrait
 * @author ntd1712
 */
trait LaravelExceptionHandlerTrait
{
    /**
     * {@inheritdoc}
     *
     * @param   \Exception $e
     * @return  \Exception
     * @see     \App\Exceptions\Handler
     */
    protected function prepareException(\Exception $e)
    {
        if ($e instanceof ValidationException || $e instanceof ServiceException
            || $e instanceof ConstraintViolationException) {
            $e = new HttpException($e->getCode(), $e->getMessage(), $e);
        } elseif ($e instanceof JWTException || $e instanceof IdentityProviderException) {
            $e = new AuthenticationException;
        } elseif ($e instanceof DecryptException) {
            $e = new HttpException(419, $e->getMessage(), $e);
        }

        return parent::prepareException($e);
    }
}
