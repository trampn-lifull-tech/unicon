<?php

namespace App\Http\Controllers;

use Chaos\Shared\Foundation\LaravelRestController;

/**
 * Class Controller
 * @author ntd1712
 */
class Controller extends LaravelRestController
{
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
                'base_dir' => $basePath,
                'base_url' => $cfg->get('app.url')
            ]
        ];

        parent::__construct($config);
    }
}
