<?php

namespace Chaos\Component\User\Entity;

/**
 * Class User
 * @author ntd1712
 *
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="Chaos\Component\User\Repository\UserRepository")
 * @Doctrine\ORM\Mapping\EntityListeners({ "Chaos\Component\User\Event\UserListener" })
 * @Doctrine\ORM\Mapping\Table(name="user")
 */
class User// extends Entity
{
    // use IdentityEntityAwareTrait, AuditEntityAwareTrait;

    // TODO
}
