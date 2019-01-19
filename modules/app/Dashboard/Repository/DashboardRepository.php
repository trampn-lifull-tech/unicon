<?php

namespace Chaos\Module\Dashboard\Repository;

use Chaos\Repository\DoctrineRepository;

/**
 * Class DashboardRepository
 * @author ntd1712
 */
class DashboardRepository extends DoctrineRepository
{
    /**
     * @var string
     */
    protected $_entityName = 'Chaos\Module\Lookup\Entity\LookupEntity';
}
