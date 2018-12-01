<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Chaos\Shared\Foundation\LaravelRestController;

class Controller extends LaravelRestController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * {@inheritdoc} @override
     */
    public function __construct()
    {
        $cfg = app('config');
        $config = glob(($basePath = base_path()) . '/packages/modules/src/*/config.yml', GLOB_NOSORT);
        array_unshift($config, $basePath . '/packages/modules/config/config.yml');

        $config['__options__'] = [
            'cache' => 'production' === $cfg->get('app.env'),
            'cache_path' => $basePath . '/storage/framework', #/vars
            'loaders' => ['yaml'],
            'merge_globals' => false,
            'replacements' => [
                'APP_DIR' => $basePath,
                'APP_URL' => $cfg->get('app.url'),
                'APP_KEY' => $cfg->get('app.key')
            ]
        ];

        parent::__construct(glob($basePath . '/packages/modules/src/*/services.yml', GLOB_NOSORT), $config);
    }
}
