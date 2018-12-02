<?php

namespace Chaos\Component\Lookup\Entity;

/**
 * Class Lookup
 * @author ntd1712
 *
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="Chaos\Component\Lookup\Repository\LookupRepository", readOnly=true)
 * @Doctrine\ORM\Mapping\EntityListeners({ "Chaos\Component\Lookup\Event\LookupListener" })
 * @Doctrine\ORM\Mapping\Table(name="lookup")
 */
class Lookup // extends Entity
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
