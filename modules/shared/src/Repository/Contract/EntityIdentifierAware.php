<?php

namespace Chaos\Common\Repository\Contract;

/**
 * Trait EntityIdentifierAware
 * @author ntd1712
 */
trait EntityIdentifierAware
{
    /**
     * @JMS\Serializer\Annotation\Exclude()
     */
    private static $sx39uqvq = [];

    /**
     * Gets the identifier of an entity.
     *
     * @return  array
     */
    public function getEntityIdentifier()
    {
        return self::$sx39uqvq;
    }

    /**
     * Sets the identifier of an entity.
     *
     * @param   array $identifier The identifier values.
     * @return  static
     */
    public function setEntityIdentifier(array $identifier)
    {
        self::$sx39uqvq = $identifier;

        return $this;
    }
}
