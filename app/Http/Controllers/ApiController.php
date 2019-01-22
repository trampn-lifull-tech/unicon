<?php

namespace App\Http\Controllers;

use Chaos\Application\LaravelResourceController;
use Chaos\Support\Orm\EntityManagerFactory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * Class ApiController
 * @author ntd1712
 */
class ApiController extends LaravelResourceController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // <editor-fold desc="Initializes service container" defaultstate="collapsed">

//        $containerResources = array_merge(
//            glob($basePath . '/modules/core/src/*/services.yml', GLOB_NOSORT),
//            glob($basePath . '/modules/app/*/services.yml', GLOB_NOSORT)
//        );
        $containerResources = [];

        $this->setContainer($containerResources);

        // </editor-fold>

        // <editor-fold desc="Initializes config loader" defaultstate="collapsed">

        $basePath = base_path();
        $config = app('config');
        $config = [
            'app' => $config->get('app'),
            'session' => $config->get('session')
        ];

        $configResources = array_merge(
        //  glob($basePath . '/modules/core/src/*/config.yml', GLOB_NOSORT),
            glob($basePath . '/modules/app/*/config.yml', GLOB_NOSORT),
            [$basePath . '/modules/config.yml']
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

        $this->setVars($configResources);

        // </editor-fold>

        // <editor-fold desc="Initializes services">

        $vars = $this->getVars();
        $container = $this->getContainer();

        $container->set('config', $vars);
        $container->set('entity_manager', (new EntityManagerFactory)($container, null, $vars->getContent()));

        if (!empty($services = func_get_args())) {
            foreach ($services as $service) {
                $service($container, $vars);

                foreach ($service as $repository) { // iterate through public properties
                    if (method_exists($repository, '__invoke')) {
                        $repository($container, $vars);
                    }
                }
            }
        }

        // </editor-fold>
    }

    // <editor-fold desc="Unused in API" defaultstate="collapsed">

    /**
     * {@inheritdoc} @override
     *
     * @throws  \BadMethodCallException
     */
    public function create()
    {
        throw new \BadMethodCallException('Unknown method ' . __METHOD__);
    }

    /**
     * {@inheritdoc} @override
     *
     * @throws  \BadMethodCallException
     */
    public function edit($id)
    {
        throw new \BadMethodCallException('Unknown method ' . __METHOD__);
    }

    // </editor-fold>
}
