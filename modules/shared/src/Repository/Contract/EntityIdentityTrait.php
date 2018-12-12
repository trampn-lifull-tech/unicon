<?php

namespace Chaos\SharedModule\Repository\Contract;

/**
 * Trait EntityIdentityTrait
 * @author ntd1712
 */
trait EntityIdentityTrait
{
    /**
     * @Doctrine\ORM\Mapping\Column(name="id", type="integer", options={"unsigned"=true})
     * @Doctrine\ORM\Mapping\GeneratedValue
     * @Doctrine\ORM\Mapping\Id
     */
    protected $Id;

    /**
     * @Doctrine\ORM\Mapping\Column(name="guid", type="guid", nullable=true)
     */
    protected $Guid;

    /**
     * @Doctrine\ORM\Mapping\Column(name="application_key", type="string", nullable=true)
     */
    private $ApplicationKey;

    /**
     * @return  integer
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @param   int $Id
     * @return  self
     */
    public function setId($Id)
    {
        $this->Id = $Id;

        return $this;
    }

    /**
     * @return  string
     */
    public function getGuid()
    {
        return $this->Guid;
    }

    /**
     * @param   string $Guid
     * @return  self
     */
    public function setGuid($Guid)
    {
        $this->Guid = $Guid;

        return $this;
    }

    /**
     * @return  string
     */
    public function getApplicationKey()
    {
        return $this->ApplicationKey;
    }

    /**
     * @param   string $ApplicationKey
     * @return  self
     */
    public function setApplicationKey($ApplicationKey)
    {
        $this->ApplicationKey = $ApplicationKey;

        return $this;
    }
}
