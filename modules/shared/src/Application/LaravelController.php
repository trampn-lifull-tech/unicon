<?php

namespace Chaos\Application;

use Chaos\Support\Contract\ConfigAware;
use Chaos\Support\Contract\ContainerAware;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;

/**
 * Class LaravelController
 * @author ntd1712
 *
 * @property \Chaos\Service\Contract\IServiceHandler $service
 */
abstract class LaravelController extends Controller
{
    use ConfigAware, ContainerAware, Contract\ControllerTrait;

    /**
     * Either gets a query value or all of the input and files.
     *
     * @param   null|string $key [optional] The key.
     * @param   mixed $default [optional] The default value if the parameter key does not exist.
     * @param   \Illuminate\Http\Request $request [optional] The request.
     * @return  array|mixed
     */
    protected function getRequest($key = null, $default = null, $request = null)
    {
        if (empty($request)) {
            $request = app('request');
        }

        if (isset($key)) {
            return $request->get($key, $default);
        }

        $params = $request->all();

        if (false !== $default) { // `false` is a hack to return `$params` without values below
            $params['UpdatedAt'] = date('Y-m-d H:i:s');
            $params['UpdatedBy'] = $this->getSession('loggedName', null, $request->getSession());
            $params['NotUse'] = 'false';
            $params['ApplicationKey'] = $this->getVars()->get('app.key');

            try {
                $params['Guid'] = Uuid::uuid4()->toString();
            } catch (\Exception $ex) {
                //
            }
        }

        return $params;
    }

    /**
     * Gets a session value or all of the session values.
     *
     * @param   null|string $key [optional] The key.
     * @param   mixed $default [optional] The default value.
     * @param   \Illuminate\Session\Store $session [optional] The session.
     * @return  array|mixed
     */
    protected function getSession($key = null, $default = null, $session = null)
    {
        if (empty($session)) {
            $session = app('session');
        }

        if (isset($key)) {
            return $session->get($key, $default);
        }

        return $session->all();
    }
}
