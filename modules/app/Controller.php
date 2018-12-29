<?php

namespace Chaos\Module;

use Chaos\Common\Application\LaravelRestController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * Class Controller
 * @author ntd1712
 */
class Controller extends LaravelRestController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Constructor.
     *
     * @throws  \Exception
     */
    public function __construct()
    {
        // <editor-fold desc="Initializes config loader" defaultstate="collapsed">

        $basePath = base_path();
        $config = config();
        $config = [
            'app' => $config->get('app'),
            'session' => $config->get('session')
        ];

        $configResources = array_merge(
            glob($basePath . '/modules/core/src/*/config.yml', GLOB_NOSORT),
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

        // <editor-fold desc="Initializes service container" defaultstate="collapsed">

//        $containerResources = array_merge(
//            glob($basePath . '/modules/core/src/*/services.yml', GLOB_NOSORT),
//            glob($basePath . '/modules/app/src/*/services.yml', GLOB_NOSORT)
//        );
        $containerResources = [];

        $this->setContainer($containerResources);

        // </editor-fold>

        parent::__construct(func_get_args());
    }
}
