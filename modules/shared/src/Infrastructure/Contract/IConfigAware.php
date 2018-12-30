<?php

namespace Chaos\Infrastructure\Contract;

/**
 * Interface IConfigAware
 * @author ntd1712
 */
interface IConfigAware
{
    /**
     * Gets a reference to the configuration object. The object returned will be of type <tt>Vars</tt>.
     *
     * @return  \M1\Vars\Vars
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
     * @param   array|\M1\Vars\Vars $config Either be an array holding the paths to the config files
     *          or a <tt>Vars</tt> instance.
     * @return  static
     */
    public function setVars($config);
}
