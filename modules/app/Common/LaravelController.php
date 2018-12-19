<?php

namespace Chaos\Module\Common;

use Chaos\Common\Contract\ConfigAware;
use Chaos\Common\Contract\ContainerAware;
use Chaos\Common\Contract\ControllerTrait;
use Chaos\Common\Orm\EntityManagerFactory;
use Chaos\Common\Service\Contract\IService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;

/**
 * Class LaravelController
 */
class LaravelController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use ConfigAware, ContainerAware, ControllerTrait;

    /**
     * Constructor.
     *
     * @throws  \Exception
     */
    public function __construct()
    {
        // <editor-fold desc="Load resources" defaultstate="collapsed">

        $basePath = base_path();
        $config = config();
        $config = [
            'app' => $config->get('app'),
            'session' => $config->get('session')
        ];

        $configResources = array_merge(
            glob($basePath . '/modules/core/src/*/config.yml', GLOB_NOSORT),
            glob($basePath . '/modules/app/*/config.yml', GLOB_NOSORT),
            [$basePath . '/modules/app/config.yml']
        );
        $configResources['__options__'] = [
            'cache' => 'production' === $config['app']['env'],
            'cache_path' => $basePath . '/storage/framework', #/vars
            'loaders' => ['yaml'],
            'merge_globals' => false,
            'replacements' => [
                'APP_DIR' => $basePath,
                'APP_FALLBACK_LOCALE' => $config['app']['fallback_locale'],
                'APP_LOCALE' => $config['app']['locale'],
                'SESSION_COOKIE' => $config['session']['cookie'],
                'SESSION_PATH' => $config['session']['path'],
                'SESSION_DOMAIN' => $config['session']['domain']
            ]
        ];

        $containerResources = [];
//        $containerResources = array_merge(
//            glob($basePath . '/modules/core/src/*/services.yml', GLOB_NOSORT),
//            glob($basePath . '/modules/app/src/*/services.yml', GLOB_NOSORT)
//        );

        // </editor-fold>

        // <editor-fold desc="Inject into container-managed objects" defaultstate="collapsed">

        $vars = $this->setVars($configResources)->getVars();
        $container = $this->setContainer($containerResources)->getContainer();

        $container->set(M1_VARS, $vars);
        $container->set(
            DOCTRINE_ENTITY_MANAGER,
            (new EntityManagerFactory)->__invoke(null, null, $vars->getContent())
        );

        if (!empty($args = func_get_args())) {
            foreach ($args as $arg) {
                if ($arg instanceof IService) {
                    $arg->setContainer($container)->setVars($vars);
                    $container->set($arg->getClass(), $arg);
                }
            }
        }

        // </editor-fold>
    }

    /**
     * Either gets a query value or all of the input and files.
     *
     * @param   null|string $key The request parameter key.
     * @param   mixed $default [optional] The default value.
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
}
