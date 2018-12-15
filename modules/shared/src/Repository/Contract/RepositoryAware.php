<?php

namespace Chaos\Common\Repository\Contract;

use Doctrine\ORM\Events;

/**
 * Trait RepositoryAware
 * @author ntd1712
 */
trait RepositoryAware
{
    private static $mwnn3h3h = [];

    /**
     * Gets a reference to the repository object. The object returned will be of type <tt>IRepository</tt>.
     *
     * <code>
     * $this->getService()->getRepository('User')->...
     * $this->getService('User')->getRepository('Role')->...
     * $this->getService('Account\Services\UserService')->getRepository('Account\Entities\Role')->...
     * </code>
     *
     * @param   null|string $name The repository name.
     * @param   bool $cache [optional] Defaults to TRUE.
     * @return  \Chaos\Common\Repository\DoctrineRepository|\Chaos\Common\Repository\Contract\IRepository
     */
    public function getRepository($name = null, $cache = true)
    {
        if (isset(self::$mwnn3h3h[$name]) && $cache) {
            return self::$mwnn3h3h[$name];
        }

        if (empty($name)) {
            $name = str_replace(['Repository', 'Service'], '', shorten(static::class));
            $repositoryName = $name . 'Repository';
        } else {
            $repositoryName = $name;
        }

        /** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
        $container = $this->getContainer();
        $config = $this->getVars();

        self::$mwnn3h3h[$repositoryName] = $container->get(DOCTRINE_ENTITY_MANAGER)
            ->getRepository(get_class($container->get($name)))
                ->setContainer($container)
                ->setVars($config);

        // register 'postLoad' listeners
        foreach (self::$mwnn3h3h[$repositoryName]->metadata->entityListeners as $event => $listeners) {
            if (Events::postLoad === $event) {
                foreach ($listeners as $listener) {
                    self::$mwnn3h3h[$repositoryName]->entityManager->getConfiguration()
                        ->getEntityListenerResolver()->register(
                            $container->get($listener['class'])
                                ->setContainer($container)
                                ->setVars($config)
                        );
                }
            }
        }

        return self::$mwnn3h3h[null] = self::$mwnn3h3h[$repositoryName];
    }
}
