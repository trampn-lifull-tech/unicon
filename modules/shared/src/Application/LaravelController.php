<?php

namespace Chaos\Application;

use Chaos\Infrastructure\Contract\ConfigAware;
use Chaos\Infrastructure\Contract\ContainerAware;
use Chaos\Infrastructure\Orm\EntityManagerFactory;
use Chaos\Service\Contract\IService;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;

/**
 * Class LaravelController
 * @author ntd1712
 *
 * @property IService $service
 */
abstract class LaravelController extends Controller
{
    use ConfigAware, ContainerAware, Contract\ControllerTrait;

    /**
     * Constructor.
     *
     * @param   array $services An array of service containers that should be initialized.
     * @throws  \Exception
     */
    public function __construct(array $services)
    {
        // <editor-fold desc="Initializes some defaults" defaultstate="collapsed">

        $vars = $this->getVars();
        $container = $this->getContainer();

        if (!empty($services)) {
            foreach ($services as $service) {
                if ($service instanceof IService) {
                    $service->setContainer($container)->setVars($vars);
                    $container->set($service->getClass(), $service);
                }
            }
        }

        $container->set(M1_VARS, $vars);
        $container->set(
            DOCTRINE_ENTITY_MANAGER,
            (new EntityManagerFactory)->__invoke(null, null, $vars->getContent())
        );

        // </editor-fold>
    }

    /**
     * Either gets a query value or all of the input and files.
     *
     * @param   null|string $key The key.
     * @param   mixed $default [optional] The default value if the parameter key does not exist.
     * @param   \Illuminate\Http\Request $request The request.
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
            $params['CreatedAt'] = 'now';
            $params['CreatedBy'] = app('session')->get('loggedName');
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
     * @param   null|string $key The key.
     * @param   mixed $default [optional] The default value.
     * @param   \Illuminate\Session\Store $session The session.
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
