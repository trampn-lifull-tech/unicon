<?php

namespace Chaos\Infrastructure\Contract;

use M1\Vars\Vars;

/**
 * Trait ConfigAware
 * @author ntd1712
 */
trait ConfigAware // implements IConfigAware
{
    // <editor-fold desc="IConfigAware implementation">

    /**
     * @JMS\Serializer\Annotation\Exclude()
     */
    private static $kba2xzbm;

    /**
     * {@inheritdoc}
     *
     * @return  \M1\Vars\Vars
     */
    public function getVars()
    {
        return self::$kba2xzbm;
    }

    /**
     * {@inheritdoc}
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

            if (empty($config)) {
                $config = new Vars($resource, $options);
            } else {
                if (isset($config[$optionKey])) {
                    $options = $config[$optionKey];
                    unset($config[$optionKey]);
                }

                array_unshift($config, $resource);
                $config = new Vars($config, $options);
            }
        }

        self::$kba2xzbm = $config;

        return $this;
    }

    // </editor-fold>
}
