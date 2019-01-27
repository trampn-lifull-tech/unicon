<?php

namespace Chaos\Support\Config\Contract;

use Chaos\Support\Config\Config;

/**
 * Trait ConfigAware
 * @author ntd1712
 */
trait ConfigAware // implements ConfigAwareInterface
{
    /**
     * @JMS\Serializer\Annotation\Exclude()
     */
    private static $kba2xzbm;

    /**
     * {@inheritdoc}
     *
     * @return  Config|\Chaos\Support\Config\Contract\ConfigInterface
     */
    public function getVars()
    {
        return self::$kba2xzbm;
    }

    /**
     * {@inheritdoc}
     *
     * @param   object|array $config Either be an array holding the paths to the resource files
     *          or a <tt>ConfigInterface</tt> instance.
     * @param   string $optionsKey [optional]
     * @return  static
     */
    public function setVars($config, $optionsKey = '__options__')
    {
        if (!$config instanceof ConfigInterface) {
            $resource = __DIR__ . '/../../../../config/config.yml';
            $options = ['cache' => false, 'loaders' => ['yaml'], 'merge_globals' => false];

            if (empty($config)) {
                $config = new Config($resource, $options);
            } else {
                if (isset($config[$optionsKey])) {
                    $options = $config[$optionsKey];
                    unset($config[$optionsKey]);
                }

                array_unshift($config, $resource);
                $config = new Config($config, $options);
            }
        }

        self::$kba2xzbm = $config;

        return $this;
    }
}
