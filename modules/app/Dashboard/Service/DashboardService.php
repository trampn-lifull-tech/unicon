<?php

namespace Chaos\Module\Dashboard\Service;

use Chaos\Module\Dashboard\Repository\DashboardRepository;
use Chaos\Service\ServiceHandler;

/**
 * Class DashboardService
 * @author ntd1712
 */
class DashboardService extends ServiceHandler
{
    /**
     * @param   DashboardRepository $dashboardRepository
     */
    public function __construct(DashboardRepository $dashboardRepository)
    {
        $this->repository = $dashboardRepository;
    }
}
