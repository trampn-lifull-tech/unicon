<?php

namespace Chaos\Support\Config\Contract;

/**
 * Interface IVarsAware
 * @author ntd1712
 */
interface IVarsAware
{
    /**
     * Gets a reference to the configuration object. The object returned will be of type <tt>IVars</tt>.
     *
     * @return  \Chaos\Support\Config\Contract\IVars
     */
    public function getVars();

    /**
     * Sets a reference to the configuration object.
     *
     * <code>
     * $this->setVars([
     *     '/modules/core/src/Lookup/config.yml',
     *     '/modules/app/Dashboard/config.yml',
     *     '/modules/config.yml',
     *     '__options__' => [
     *         'cache' => false,
     *         'cache_path' => '/storage/framework',
     *         'loaders' => ['yaml'],
     *         'merge_globals' => false,
     *         'replacements' => [
     *             'APP_DIR' => base_path(),
     *             // ...
     *         ]
     *     ]
     * ]);
     * $this->setVars([]);
     * </code>
     *
     * @param   object|array $vars Either be an array holding the paths to the resource files
     *          or a <tt>IVars</tt> instance.
     * @return  static
     */
    public function setVars($vars);
}
