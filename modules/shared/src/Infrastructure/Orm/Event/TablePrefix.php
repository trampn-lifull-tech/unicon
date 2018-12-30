<?php

namespace Chaos\Infrastructure\Orm\Event;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Class TablePrefix
 *
 * @link http://doctrine-project.org/projects/doctrine-orm/en/latest/cookbook/sql-table-prefixes.html
 */
final class TablePrefix implements EventSubscriber
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * Constructor.
     *
     * @param   string $prefix The column prefix.
     */
    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * {@inheritdoc}
     *
     * @return  array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata
        ];
    }

    /**
     * @param   \Doctrine\ORM\Event\LoadClassMetadataEventArgs $args The event arguments.
     * @return  void
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $metadata = $args->getClassMetadata();
        $mappings = $metadata->getAssociationMappings();

        if (!$metadata->isInheritanceTypeSingleTable() || $metadata->rootEntityName === $metadata->getName()) {
            $metadata->setPrimaryTable(['name' => $this->prefix . $metadata->getTableName()]);
        }

        foreach ($mappings as $fieldName => $mapping) {
            if (ClassMetadataInfo::MANY_TO_MANY == $mapping['type'] && $mapping['isOwningSide']) {
                $metadata->associationMappings[$fieldName]['joinTable']['name']
                    = $this->prefix . $mapping['joinTable']['name'];
            }
        }
    }
}
