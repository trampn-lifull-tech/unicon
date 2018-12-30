<?php

namespace Chaos\Repository\Contract;

/**
 * Trait EntityAuditTrait
 * @author ntd1712
 */
trait EntityAuditTrait
{
    /**
     * @Doctrine\ORM\Mapping\Column(name="created_at", type="datetime", nullable=true)
     */
    private $CreatedAt;

    /**
     * @Doctrine\ORM\Mapping\Column(name="created_by", type="string", nullable=true)
     */
    private $CreatedBy;

    /**
     * @Doctrine\ORM\Mapping\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $UpdatedAt;

    /**
     * @Doctrine\ORM\Mapping\Column(name="updated_by", type="string", nullable=true)
     */
    private $UpdatedBy;

    /**
     * @Doctrine\ORM\Mapping\Column(name="not_use", type="boolean", nullable=true)
     */
    private $NotUse;

    /**
     * @Doctrine\ORM\Mapping\Column(name="version", type="integer", nullable=true)
     * @Doctrine\ORM\Mapping\Version
     */
    private $Version;

    /**
     * @return  \DateTime
     */
    public function getAddedAt()
    {
        return $this->CreatedAt;
    }

    /**
     * @param   \DateTime $CreatedAt
     * @return  self
     */
    public function setCreatedAt($CreatedAt)
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }

    /**
     * @return  string
     */
    public function getCreatedBy()
    {
        return $this->CreatedBy;
    }

    /**
     * @param   string $CreatedBy
     * @return  self
     */
    public function setCreatedBy($CreatedBy)
    {
        $this->CreatedBy = $CreatedBy;

        return $this;
    }

    /**
     * @return  \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->UpdatedAt;
    }

    /**
     * @param   \DateTime $UpdatedAt
     * @return  self
     */
    public function setUpdatedAt($UpdatedAt)
    {
        $this->UpdatedAt = $UpdatedAt;

        return $this;
    }

    /**
     * @return  string
     */
    public function getUpdatedBy()
    {
        return $this->UpdatedBy;
    }

    /**
     * @param   string $UpdatedBy
     * @return  self
     */
    public function setUpdatedBy($UpdatedBy)
    {
        $this->UpdatedBy = $UpdatedBy;

        return $this;
    }

    /**
     * @return  bool
     */
    public function getNotUse()
    {
        return $this->NotUse;
    }

    /**
     * @param   bool $NotUse
     * @return  self
     */
    public function setNotUse($NotUse)
    {
        $this->NotUse = $NotUse;

        return $this;
    }

    /**
     * @return  int
     */
    public function getVersion()
    {
        return $this->Version;
    }

    /**
     * @param   int $Version
     * @return  self
     */
    public function setVersion($Version)
    {
        $this->Version = $Version;

        return $this;
    }
}
