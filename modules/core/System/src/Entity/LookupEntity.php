<?php

namespace Chaos\CoreModule\System\Entity;

// use Chaos\CoreModule\Repository\Contract;
// use Chaos\CoreModule\Repository\Entity;

/**
 * Class LookupEntity
 * @author ntd1712
 *
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="Chaos\CoreModule\System\Repository\LookupRepository", readOnly=true)
 * @Doctrine\ORM\Mapping\EntityListeners({ "Chaos\CoreModule\System\Event\LookupListener" })
 * @Doctrine\ORM\Mapping\Table(name="lookup")
 */
class LookupEntity // extends Entity
{
    // use Contract\EntityIdentityTrait, Contract\EntityAuditTrait;

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
