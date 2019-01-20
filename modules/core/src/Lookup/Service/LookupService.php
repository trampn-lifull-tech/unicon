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
    public function __construct(LookupRepository $lookupRepository)
    {
        $this->repository = $lookupRepository;
    }

    // <editor-fold desc="For illustration only" defaultstate="collapsed">
    /*
    public function __construct(LookupRepository $lookupRepository,
                                \Chaos\Module\Dashboard\Repository\DashboardRepository $dashboardRepository,
                                \Chaos\Module\Dashboard\Service\DashboardService $dashboardService)
    {
        $this->repository = $lookupRepository;
        $this->dashboardRepository = $dashboardRepository;
        $this->dashboardService = $dashboardService;
    }
    */
    // </editor-fold>
}
