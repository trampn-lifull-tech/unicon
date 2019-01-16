<?php

namespace Chaos\Module\Lookup\Service;

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
    public function __construct(LookupRepository $lookupRepository, \Chaos\Module\Dashboard\Service\DashboardService $dashboardService)
    {
        $this->repository = $lookupRepository;
        $this->dashboardService = $dashboardService;
    }

    /**
     * For testing purpose only.
     */
    public function test()
    {
        return $this->dashboardService;
    }

    private $dashboardService;
}
