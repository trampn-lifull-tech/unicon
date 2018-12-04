<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Chaos\Common\Application\LaravelRestController;

/**
 * Class Controller
 */
class Controller extends LaravelRestController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @todo Move this to somewhere, we don't want to load config by constructor
     *
     * {@inheritdoc} @override
     */
    public function __construct()
    {
        $basePath = base_path();
        $config = config();
        $config = [
            'app' => $config->get('app'),
            'session' => $config->get('session')
        ];

        $configResources = array_merge(
            // glob($basePath . '/modules/core/*/config/config.yml', GLOB_NOSORT),
            glob($basePath . '/modules/features/*/config/config.yml', GLOB_NOSORT),
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
        $containerResources = array_merge(
            // glob($basePath . '/modules/core/*/config/services.yml', GLOB_NOSORT),
            glob($basePath . '/modules/features/*/config/services.yml', GLOB_NOSORT)
        );

        parent::__construct($containerResources, $configResources);
    }
}
