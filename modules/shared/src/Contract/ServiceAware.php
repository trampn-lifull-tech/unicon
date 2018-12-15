<?php

namespace Chaos\Common\Contract;

/**
 * Trait ServiceAware
 * @author ntd1712
 *
 * @method \Symfony\Component\DependencyInjection\ContainerBuilder|\Symfony\Component\DependencyInjection\ContainerInterface getContainer()
 * @method \M1\Vars\Vars getVars()
 */
trait ServiceAware
{
    private static $vcx79te3 = [];

    /**
     * Gets a reference to the service object. The object returned will be of type <tt>IService</tt>.
     *
     * <code>
     * $this->getService()->...
     * $this->getService('Lookup')->...
     * $this->getService('Chaos\Module\Lookup\Service\LookupService')->...
     * </code>
     *
     * @param   null|string $name The service name.
     * @param   bool $cache [optional] Defaults to TRUE.
     * @return  \Chaos\Common\Service\Service|\Chaos\Common\Service\Contract\IService
     */
    public function getService($name = null, $cache = true)
    {
        if (isset(self::$vcx79te3[$name]) && $cache) {
            return self::$vcx79te3[$name];
        }

        if (empty($name)) {
            $serviceName = str_replace(['Controller', 'Service'], '', shorten(static::class)) . 'Service';
        } else if (false === strpos($name, '\\')) {
            $serviceName = str_replace('Service', '', $name) . 'Service';
        } else {
            $serviceName = $name;
        }

        /** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
        $container = $this->getContainer();

        return self::$vcx79te3[null] = self::$vcx79te3[$serviceName] = $container->get($serviceName)
            ->setContainer($container)
            ->setVars($this->getVars());
    }
}
