<?php

namespace Chaos\Module\Lookup\Repository;

use Chaos\Repository\DoctrineRepository;

/**
 * Class LookupRepository
 * @author ntd1712
 */
class LookupRepository extends DoctrineRepository
{
    /**
     * @var string
     */
    protected $_entityName = 'Chaos\Module\Lookup\Entity\LookupEntity';
}
