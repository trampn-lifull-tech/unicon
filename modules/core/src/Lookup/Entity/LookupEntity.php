<?php

namespace Chaos\Module\Lookup\Entity;

use Chaos\Repository\Contract\EntityAuditTrait;
use Chaos\Repository\Contract\EntityIdentityTrait;
use Chaos\Repository\Entity;

/**
 * Class LookupEntity
 * @author ntd1712
 *
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="Chaos\Module\Lookup\Repository\LookupRepository", readOnly=true)
 * @Doctrine\ORM\Mapping\EntityListeners({ "Chaos\Module\Lookup\Event\LookupListener" })
 * @Doctrine\ORM\Mapping\Table(name="lookup")
 */
class LookupEntity extends Entity
{
    use EntityIdentityTrait, EntityAuditTrait;

    /**
     * @Doctrine\ORM\Mapping\Column(name="type", type="string")
     */
    protected $Type;

    /**
     * @Doctrine\ORM\Mapping\Column(name="code", type="integer")
     */
    protected $Code;

    /**
     * @Doctrine\ORM\Mapping\Column(name="sort_order", type="integer", nullable=true, options={"unsigned"=true})
     */
    protected $SortOrder;
}
