<?php

namespace Chaos\Component\Permission\Entity;

/**
 * Class Permission
 * @author ntd1712
 *
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="Chaos\Component\Permission\Repository\PermissionRepository", readOnly=true)
 * @Doctrine\ORM\Mapping\EntityListeners({ "Chaos\Component\Permission\Event\PermissionListener" })
 * @Doctrine\ORM\Mapping\Table(name="Permission")
 */
class Permission// extends Entity
{
    // use IdentityEntityAwareTrait, AuditEntityAwareTrait;

    // TODO
}
