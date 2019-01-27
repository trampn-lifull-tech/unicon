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
     * @param   object|array $vars Either be an array holding the paths to the resource files
     *          or a <tt>ConfigInterface</tt> instance.
     * @param   string $optionsKey [optional]
     * @return  static
     */
    public function setVars($vars, $optionsKey = '__options__')
    {
        if (!$vars instanceof ConfigInterface) {
            $config = new Config;
            $resource = __DIR__ . '/../../../../config/config.yml';
            $options = ['cache' => false, 'loaders' => ['yaml'], 'merge_globals' => false];

            if (empty($vars)) {
                $vars = $config($resource, $options);
            } else {
                if (isset($vars[$optionsKey])) {
                    $options = $vars[$optionsKey];
                    unset($vars[$optionsKey]);
                }

                array_unshift($vars, $resource);
                $vars = $config($vars, $options);
            }
        }

        self::$kba2xzbm = $vars;

        return $this;
    }
}
