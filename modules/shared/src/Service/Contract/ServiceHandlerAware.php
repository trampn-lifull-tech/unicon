<?php

namespace Chaos\Service\Contract;

/**
 * Trait ServiceHandlerAware
 * @author ntd1712
 *
 * @deprecated Not in use
 */
trait ServiceHandlerAware
{
    private static $vcx79te3 = [];

    /**
     * Gets a reference to the service object. The object returned will be of type <tt>IService</tt>.
     *
     * <code>
     * $this->getService()->...
     * $this->getService('User')->...
     * $this->getService('Account\Services\UserService')->...
     * </code>
     *
     * @param   null|string $name The service name.
     * @param   bool $cache [optional] Defaults to TRUE.
     * @return  \Chaos\Service\Contract\IServiceHandler
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
        $vars = $this->getVars();

        return self::$vcx79te3[null] = self::$vcx79te3[$serviceName] = $container->get($serviceName)
            ->setContainer($container)
            ->setVars($vars);
    }
}
