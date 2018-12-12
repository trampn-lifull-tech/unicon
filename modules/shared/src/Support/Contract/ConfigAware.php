<?php

namespace Chaos\SharedModule\Support\Contract;

use M1\Vars\Vars;

/**
 * Trait ConfigAware
 * @author ntd1712
 */
trait ConfigAware
{
    /**
     * @JMS\Serializer\Annotation\Exclude()
     */
    private static $kba2xzbm;

    /**
     * Gets a reference to the configuration object. The object returned will be of type <tt>Vars</tt>.
     *
     * @return  \M1\Vars\Vars
     */
    public function getVars()
    {
        return self::$kba2xzbm;
    }

    /**
     * Sets a reference to the configuration object.
     *
     * @param   array|\M1\Vars\Vars $config Either be an array holding the paths to the config files
     *          or a <tt>Vars</tt> instance.
     * @param   string $optionKey [optional]
     * @return  static
     */
    public function setVars($config, $optionKey = '__options__')
    {
        if (!$config instanceof Vars) {
            $resource = __DIR__ . '/../../../config/config.yml';
            $options = ['cache' => false, 'loaders' => ['yaml'], 'merge_globals' => false];

            if (isset($config)) {
                if (isset($config[$optionKey])) {
                    $options = $config[$optionKey];
                    unset($config[$optionKey]);
                }

                array_unshift($config, $resource);
                $config = new Vars($config, $options);
            } else {
                $config = new Vars($resource, $options);
            }
        }

        self::$kba2xzbm = $config;

        return $this;
    }
}
