<?php

namespace Chaos\Component\Permission;

/**
 * Class PermissionEntity
 * @author ntd1712
 *
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="Chaos\Component\Permission\PermissionRepository")
 * @Doctrine\ORM\Mapping\EntityListeners({ "Chaos\Component\Permission\PermissionListener" })
 * @Doctrine\ORM\Mapping\Table(name="permission")
 */
class PermissionEntity // extends Entity
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
