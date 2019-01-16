<?php

namespace Chaos\Support\Config\Contract;

use Chaos\Support\Config\Vars;

/**
 * Trait VarsAware
 * @author ntd1712
 */
trait VarsAware // implements IVarsAware
{
    /**
     * @JMS\Serializer\Annotation\Exclude()
     */
    private static $kba2xzbm;

    /**
     * {@inheritdoc}
     *
     * @return  Vars|\Chaos\Support\Config\Contract\IVars
     */
    public function getVars()
    {
        return self::$kba2xzbm;
    }

    /**
     * {@inheritdoc}
     *
     * @param   object|array $vars Either be an array holding the paths to the resource files
     *          or a <tt>IVars</tt> instance.
     * @param   string $optionsKey [optional]
     * @return  static
     */
    public function setVars($vars, $optionsKey = '__options__')
    {
        if (!$vars instanceof IVars) {
            $resource = __DIR__ . '/../../../../config/config.yml';
            $options = ['cache' => false, 'loaders' => ['yaml'], 'merge_globals' => false];

            if (empty($vars)) {
                $vars = new Vars($resource, $options);
            } else {
                if (isset($vars[$optionsKey])) {
                    $options = $vars[$optionsKey];
                    unset($vars[$optionsKey]);
                }

                array_unshift($vars, $resource);
                $vars = new Vars($vars, $options);
            }
        }

        self::$kba2xzbm = $vars;

        return $this;
    }
}
