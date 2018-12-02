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
            glob($basePath . '/packages/components/*/config/config.yml', GLOB_NOSORT),
            glob($basePath . '/packages/modules/*/config/config.yml', GLOB_NOSORT),
            [$basePath . '/packages/config.yml']
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
            glob($basePath . '/packages/components/*/config/services.yml', GLOB_NOSORT),
            glob($basePath . '/packages/modules/*/config/services.yml', GLOB_NOSORT)
        );

        parent::__construct($containerResources, $configResources);
    }
}
