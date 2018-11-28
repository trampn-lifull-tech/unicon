<?php

namespace System\Entity;

use Shared\Foundation\Entity;
use Shared\Foundation\Traits\AuditEntityAwareTrait;
use Shared\Foundation\Traits\IdentityEntityAwareTrait;

/**
 * Class Lookup
 * @author ntd1712
 *
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="System\Repositories\LookupRepository", readOnly=true)
 * @Doctrine\ORM\Mapping\EntityListeners({ "System\Events\LookupListener" })
 * @Doctrine\ORM\Mapping\Table(name="lookup")
 */
class Lookup extends Entity
{
    use IdentityEntityAwareTrait, AuditEntityAwareTrait;

    /**
     * [NotEmpty|HtmlEntities]
     *
     * @Doctrine\ORM\Mapping\Column(name="name", type="string")
     */
    protected $Name;
    /**
     * [NotEmpty]
     *
     * @Doctrine\ORM\Mapping\Column(name="code", type="integer")
     */
    protected $Code;
    /**
     * [NotEmpty]
     *
     * @Doctrine\ORM\Mapping\Column(name="type", type="string")
     */
    protected $Type;
    /**
     * @Doctrine\ORM\Mapping\Column(name="position", type="integer", nullable=true, options={"unsigned"=true})
     */
    protected $Position;
}
