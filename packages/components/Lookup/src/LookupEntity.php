<?php

namespace Chaos\Component\Lookup;

/**
 * Class LookupEntity
 * @author ntd1712
 *
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="Chaos\Component\Lookup\LookupRepository", readOnly=true)
 * @Doctrine\ORM\Mapping\EntityListeners({ "Chaos\Component\Lookup\LookupListener" })
 * @Doctrine\ORM\Mapping\Table(name="lookup")
 */
class LookupEntity // extends Entity
{
    // use IdentityAware, AuditAware;

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
