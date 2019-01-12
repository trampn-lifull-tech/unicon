<?php

namespace Chaos\Support\Orm;

use Doctrine\Common\Cache;
use Doctrine\Common\EventManager;
use Doctrine\Common\Persistence\Mapping\Driver as PHPDriver;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM;
use Doctrine\ORM\Mapping\Driver;
use Psr\Container\ContainerInterface;

/**
 * Class EntityManagerFactory
 * @author ntd1712
 */
final class EntityManagerFactory // implements \Zend\ServiceManager\Factory\FactoryInterface
{
    // <editor-fold desc="FactoryInterface implementation">

    /**
     * {@inheritdoc}
     *
     * @param   \Psr\Container\ContainerInterface $container The container object.
     * @param   string $requestedName [optional]
     * @param   null|array $options [optional]
     * @return  \Doctrine\ORM\EntityManager|\Doctrine\ORM\EntityManagerInterface
     * @throws  \Doctrine\DBAL\DBALException
     * @throws  \Doctrine\ORM\ORMException
     */
    public function __invoke(ContainerInterface $container = null, $requestedName = null, array $options = null)
    {
        if (empty($options)) {
            $options = $container->get(M1_VARS);
        }

        $entityManager = ORM\EntityManager::create(
            $this->getConnectionParams($dbal = $options['doctrine']['dbal']),
            $this->getConfiguration($dbal, $options['doctrine']['orm']),
            $this->getEventManager($dbal['schema_filter'])
        );
        $entityManager->getConfiguration()->setDefaultQueryHint('options', $options);
        $this->registerTypes($dbal, $entityManager);

        /*\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(function ($class) {
            return (bool) class_exists($class); // ensure an attempt to autoload an annotation class is made
        });*/

        return $entityManager;
    }

    // </editor-fold>

    /**
     * @param   array $dbal The DBAL config.
     * @return  array
     */
    private function getConnectionParams($dbal)
    {
        return $dbal['connections'][$dbal['default_connection']];
    }

    /**
     * @param   array $dbal The DBAL config.
     * @param   array $orm The ORM config.
     * @return  \Doctrine\ORM\Configuration
     * @throws  \Doctrine\ORM\ORMException
     */
    private function getConfiguration($dbal, $orm)
    {
        $proxyDir = $orm['proxy_dir'] ?: sys_get_temp_dir();
        $cacheNs = 'dc2_' . md5($proxyDir) . '_';
        $config = $orm['entity_managers'][$orm['default_entity_manager']];

        $configuration = new ORM\Configuration;
        $configuration->setAutoCommit($dbal['connections'][$config['connection']]['auto_commit']);
        $configuration->setSQLLogger($dbal['connections'][$config['connection']]['logging']);
        // $configuration->setHydrationCacheImpl($this->getCacheDriver($config['hydration_cache_driver'], $cacheNs));
        $configuration->setMetadataCacheImpl($this->getCacheDriver($config['metadata_cache_driver'], $cacheNs));
        $configuration->setQueryCacheImpl($this->getCacheDriver($config['query_cache_driver'], $cacheNs));
        $configuration->setResultCacheImpl($this->getCacheDriver($config['result_cache_driver'], $cacheNs));
        $configuration->setAutoGenerateProxyClasses($orm['auto_generate_proxy_classes']);
        $configuration->setProxyDir($proxyDir);
        $configuration->setProxyNamespace($orm['proxy_namespace']);
        $configuration->setMetadataDriverImpl($this->getMetadataDriver($config['mappings'], $configuration));
        $configuration->setClassMetadataFactoryName($config['class_metadata_factory_name']);
        $configuration->setDefaultRepositoryClassName($config['default_repository_class']);

        if (!empty($config['repository_factory'])) {
            $configuration->setRepositoryFactory($config['repository_factory']);
        }

        if (!empty($config['dql']['datetime_functions'])) {
            $configuration->setCustomDatetimeFunctions((array)$config['dql']['datetime_functions']);
        }

        if (!empty($config['dql']['numeric_functions'])) {
            $configuration->setCustomNumericFunctions((array)$config['dql']['numeric_functions']);
        }

        if (!empty($config['dql']['string_functions'])) {
            $configuration->setCustomStringFunctions((array)$config['dql']['string_functions']);
        }

        if (!empty($config['ast'])) {
            $configuration->setDefaultQueryHints($config['ast']);
        }

        return $configuration;
    }

    /**
     * @param   array $config The config.
     * @param   string $namespace The namespace.
     * @return  \Doctrine\Common\Cache\Cache
     */
    private function getCacheDriver($config, $namespace)
    {
        switch ($config['type']) {
            case 'array':
                $cache = new Cache\ArrayCache;
                break;
            case 'redis':
                if (extension_loaded('redis')) {
                    $redis = new \Redis;
                    $redis->connect(
                        $config['host'],
                        $config['port'],
                        isset($config['timeout']) ? $config['timeout'] : 0.0,
                        isset($config['reserved']) ? $config['reserved'] : null,
                        isset($config['retry_interval']) ? $config['retry_interval'] : 0
                    );
                    $redis->select(isset($config['dbIndex']) ? $config['dbIndex'] : 0);

                    $cache = new Cache\RedisCache;
                    $cache->setRedis($redis);
                }
                break;
            case 'memcache':
                if (extension_loaded('memcache')) {
                    $memcache = new \Memcache;
                    $memcache->connect(
                        $config['host'],
                        $config['port'],
                        isset($config['timeout']) ? $config['timeout'] : 1
                    );

                    $cache = new Cache\MemcacheCache;
                    $cache->setMemcache($memcache);
                }
                break;
            case 'xcache':
                if (extension_loaded('xcache')) {
                    $cache = new Cache\XcacheCache;
                }
                break;
            case 'apcu':
                if (extension_loaded('apcu')) {
                    $cache = new Cache\ApcuCache;
                }
                break;
            case 'filesystem':
                $cache = new Cache\FilesystemCache(
                    $config['directory'],
                    isset($config['extension']) ? $config['extension'] : Cache\FilesystemCache::EXTENSION,
                    isset($config['umask']) ? $config['umask'] : 0002
                );
                break;
            case 'custom':
                $class = $config['class'];
                $instance = $config['instance_class'];
                $method = 'set' . shorten($instance);
                unset($config['type'], $config['instance_class'], $config['class']);

                $instance = new $instance;
                call_user_func_array([$instance, 'connect'], $config);

                $cache = new $class;
                $cache->$method($instance);
                break;
            default:
        }

        if (empty($cache)) {
            throw new \RuntimeException(sprintf('Unsupported cache driver: %s', $config['type']));
        }

        if ($cache instanceof Cache\CacheProvider) {
            $cache->setNamespace($namespace);
        }

        return $cache;
    }

    /**
     * @param   array $mappings An array of mappings.
     * @param   \Doctrine\ORM\Configuration $configuration The configuration object instance.
     * @return  null|\Doctrine\Common\Persistence\Mapping\Driver\MappingDriver
     */
    private function getMetadataDriver(array $mappings, ORM\Configuration $configuration)
    {
        foreach ($mappings as $config) {
            if (true === $config['mapping']) {
                switch ($config['type']) {
                    case 'annotation':
                        return $configuration->newDefaultAnnotationDriver(
                            $config['dir'],
                            isset($config['use_simple_annotation_reader'])
                                ? $config['use_simple_annotation_reader'] : true
                        );
                    case 'yaml':
                        return new Driver\YamlDriver(
                            $config['dir'],
                            isset($config['fileExtension'])
                                ? $config['fileExtension'] : Driver\YamlDriver::DEFAULT_FILE_EXTENSION
                        );
                    case 'xml':
                        return new Driver\XmlDriver(
                            $config['dir'],
                            isset($config['fileExtension'])
                                ? $config['fileExtension'] : Driver\XmlDriver::DEFAULT_FILE_EXTENSION
                        );
                    case 'php':
                        return new PHPDriver\PHPDriver($config['dir']);
                    case 'staticphp':
                        return new PHPDriver\StaticPHPDriver($config['dir']);
                    default:
                }
            }
        }

        return null;
    }

    /**
     * @param   string $prefix [optional]
     * @return  \Doctrine\Common\EventManager
     */
    private function getEventManager($prefix = null)
    {
        $eventManager = new EventManager;

        if (!empty($prefix)) {
            $eventManager->addEventListener(ORM\Events::loadClassMetadata, new Event\TablePrefix($prefix));
            // $eventManager->addEventSubscriber(new TablePrefix($prefix)); // or above
        }

        return $eventManager;
    }

    /**
     * @param   array $dbal The DBAL config.
     * @param   \Doctrine\ORM\EntityManager $em The EntityManager object instance.
     * @return  void
     * @throws  \Doctrine\DBAL\DBALException
     */
    private function registerTypes($dbal, $em)
    {
        if (!empty($dbal['types'])) {
            foreach ($dbal['types'] as $doctrineType => $options) {
                Type::hasType($doctrineType)
                    ? Type::overrideType($doctrineType, $options['class'])
                    : Type::addType($doctrineType, $options['class']);
            }

            if (!empty($mappingTypes = $dbal['connections'][$dbal['default_connection']]['mapping_types'])) {
                foreach ($mappingTypes as $dbType => $doctrineType) {
                    $em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping($dbType, $doctrineType);
                }
            }
        }
    }
}
