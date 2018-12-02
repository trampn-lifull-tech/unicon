<?php

namespace Chaos\Component\Role;

/**
 * Class RoleEntity
 * @author ntd1712
 *
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="Chaos\Component\Role\RoleRepository")
 * @Doctrine\ORM\Mapping\EntityListeners({ "Chaos\Component\Role\RoleListener" })
 * @Doctrine\ORM\Mapping\Table(name="role")
 */
class RoleEntity // extends Entity
{
    // use IdentityAware, AuditAware;

    /**
     * @Doctrine\ORM\Mapping\Column(name="name", type="string")
     */
    protected $Name;

    /**
     * @Doctrine\ORM\Mapping\Column(name="description", type="string", nullable=true)
     */
    protected $Description;
}
