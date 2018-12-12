<?php

namespace Chaos\FeatureModule\Bar\Entity;

// use Chaos\CoreModule\Repository\Contract;
// use Chaos\CoreModule\Repository\Entity;

/**
 * Class BarEntity
 * @author ntd1712
 *
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="Chaos\FeatureModule\Bar\Repository\BarRepository", readOnly=true)
 * @Doctrine\ORM\Mapping\EntityListeners({ "Chaos\FeatureModule\Bar\Event\BarListener" })
 * @Doctrine\ORM\Mapping\Table(name="bar")
 */
class BarEntity // extends Entity
{
    // use Contract\EntityIdentityTrait, Contract\EntityAuditTrait;
}
