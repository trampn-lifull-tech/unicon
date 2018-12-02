<?php

namespace Chaos\Component\Permission\Entity;

/**
 * Class Permission
 * @author ntd1712
 *
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="Chaos\Component\Permission\Repository\PermissionRepository")
 * @Doctrine\ORM\Mapping\EntityListeners({ "Chaos\Component\Permission\Event\PermissionListener" })
 * @Doctrine\ORM\Mapping\Table(name="permission")
 */
class Permission // extends Entity
{
    // use IdentityAwareTrait, AuditAwareTrait;
}
