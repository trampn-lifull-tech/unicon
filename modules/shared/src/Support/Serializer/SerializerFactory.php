<?php

namespace Chaos\Support\Serializer;

use JMS\Serializer\Construction;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming;
use JMS\Serializer\SerializerBuilder;
use Metadata\Cache\DoctrineCacheAdapter;
use Psr\Container\ContainerInterface;

/**
 * Class SerializerFactory
 * @author ntd1712
 */
final class SerializerFactory // implements \Zend\ServiceManager\Factory\FactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @param   \Psr\Container\ContainerInterface $container The container.
     * @param   string $requestedName [optional]
     * @param   null|array $options [optional]
     * @return  \JMS\Serializer\Serializer|\JMS\Serializer\SerializerInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName = null, array $options = null)
    {
        if (empty($options)) {
            $options = $container->get(M1_VARS);
        }

        $serializer = SerializerBuilder::create()
            ->setDebug($options['jms']['metadata']['debug'])
            ->includeInterfaceMetadata($options['jms']['metadata']['include_interface_metadata']);

        if (null !== ($cacheDir = $options['jms']['metadata']['cache_dir'])) {
            $serializer->setCacheDir($cacheDir);
        }

        if (null !== ($annotationReader = $options['jms']['metadata']['annotation_reader'])) {
            /** @var \Doctrine\Common\Annotations\Reader $annotationReader */
            $serializer->setAnnotationReader(new $annotationReader($cacheDir));
        }

        if (null !== ($metadataDirs = $options['jms']['metadata']['metadata_dirs'])) {
            $serializer->addMetadataDirs($metadataDirs);
        }

        if (null !== ($entityManager = $container->get(DOCTRINE_ENTITY_MANAGER))) {
            if (null !== ($metadataCacheImpl = $entityManager->getConfiguration()->getMetadataCacheImpl())) {
                if (is_subclass_of($metadataCacheImpl, 'Doctrine\Common\Cache\Cache')) {
                    /** @var \Doctrine\Common\Cache\CacheProvider $metadataCacheImpl */
                    $prefix = $metadataCacheImpl->getNamespace();
                } else {
                    $prefix = 'dc2_' . md5($cacheDir) . '_';
                }

                if (method_exists($serializer, 'setMetadataCache')) {
                    $serializer->setMetadataCache(new DoctrineCacheAdapter($prefix, $metadataCacheImpl));
                }
            }

            if (null !== ($objectConstructor = $options['jms']['object_constructor'])) {
                /** @var \Doctrine\Common\Persistence\ManagerRegistry $objectConstructor */
                $serializer->setObjectConstructor(new Construction\DoctrineObjectConstructor(
                    $objectConstructor,
                    new Construction\UnserializeObjectConstructor
                ));
            }
        }

        if (null !== ($handlers = $options['jms']['handlers'])) {
            /** @link https://jmsyst.com/libs/serializer/master/handlers */
            foreach ((array)$handlers as $handler) {
                $serializer->configureHandlers(function (HandlerRegistry $registry) use ($handler) {
                    $registry->registerSubscribingHandler(new $handler);
                });
            }
        }

        if (null !== ($events = $options['jms']['events'])) {
            /** @link https://jmsyst.com/libs/serializer/master/event_system */
            foreach ((array)$events as $subscriber) {
                $serializer->configureListeners(function (EventDispatcher $dispatcher) use ($subscriber) {
                    $dispatcher->addSubscriber(new $subscriber);
                });
            }
        }

        if (null !== ($propertyNamingStrategy = $options['jms']['property_naming_strategy'])) {
            $namingStrategy = @call_user_func_array(
                $propertyNamingStrategy['class_name'],
                (array)$propertyNamingStrategy['args']
            );

            if ($namingStrategy instanceof Naming\PropertyNamingStrategyInterface) {
                $propertyNamingStrategy = new Naming\SerializedNameAnnotationStrategy($namingStrategy);
                $serializer->setPropertyNamingStrategy($propertyNamingStrategy);

                if (null !== ($serializationVisitors = $options['jms']['serialization_visitors'])) {
                    foreach ((array)$serializationVisitors as $format => $visitor) {
                        $serializer->setSerializationVisitor($format, new $visitor($propertyNamingStrategy));
                    }
                }

                if (null !== ($deserializationVisitors = $options['jms']['deserialization_visitors'])) {
                    foreach ((array)$deserializationVisitors as $format => $visitor) {
                        $serializer->setDeserializationVisitor($format, new $visitor($propertyNamingStrategy));
                    }
                }
            }
        }

        if (null !== ($expressionEvaluator = $options['jms']['expression_evaluator'])) {
            /**
             * @link https://jmsyst.com/libs/serializer/master/cookbook/exclusion_strategies
             * @link http://symfony.com/doc/current/components/expression_language/extending.html
             */
            $serializer->setExpressionEvaluator(new ExpressionEvaluator(new $expressionEvaluator['language']));
        }

        if (null !== ($serializationContextFactory = $options['jms']['serialization_context_factory'])) {
            /**
             * @see \JMS\Serializer\ContextFactory\DefaultSerializationContextFactory
             * @var \JMS\Serializer\ContextFactory\SerializationContextFactoryInterface $serializationContextFactory
             */
            $serializer->setSerializationContextFactory(new $serializationContextFactory);
        }

        if (null !== ($deserializationContextFactory = $options['jms']['deserialization_context_factory'])) {
            /**
             * @see \JMS\Serializer\ContextFactory\DefaultDeserializationContextFactory
             * @var \JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface $deserializationContextFactory
             */
            $serializer->setDeserializationContextFactory(new $deserializationContextFactory);
        }

        return $serializer->build();
    }
}
