<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Chaos\Shared\Application\LaravelRestController;

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

        $resources = glob($basePath . '/packages/modules/src/*/config.yml', GLOB_NOSORT);
        array_unshift($resources, $basePath . '/packages/modules/config/config.yml');

        $resources['__options__'] = [
            'cache' => 'production' === $config['app']['env'],
            'cache_path' => $basePath . '/storage/framework', #/vars
            'loaders' => ['yaml'],
            'merge_globals' => false,
            'replacements' => [
                'APP_DIR' => $basePath,
                'APP_FALLBACK_LOCALE' => $config['app']['fallback_locale'],
                'APP_LOCALE' => $config['app']['locale'],
                'APP_TIMEZONE' => $config['app']['timezone'],
                'SESSION_COOKIE' => $config['session']['cookie'],
                'SESSION_PATH' => $config['session']['path'],
                'SESSION_DOMAIN' => $config['session']['domain'],
                'SESSION_SECURE' => $config['session']['secure'],
                'SESSION_HTTP_ONLY' => $config['session']['http_only']
            ]
        ];

        parent::__construct(glob($basePath . '/packages/modules/src/*/services.yml', GLOB_NOSORT), $resources);
    }
}
