<?php

namespace Chaos\Module\Lookup\Service;

use Chaos\Module\Dashboard\Service\DashboardService;
use Chaos\Module\Lookup\Repository\LookupRepository;
use Chaos\Service\ServiceHandler;

/**
 * Class LookupService
 * @author ntd1712
 */
class LookupService extends ServiceHandler
{
    /**
     * @param   LookupRepository $lookupRepository
     */
    public function __construct(LookupRepository $lookupRepository, DashboardService $dashboardService)
    {
        $this->repository = $lookupRepository;
        $this->dashboardService = $dashboardService;
    }

    private $dashboardService;
}
