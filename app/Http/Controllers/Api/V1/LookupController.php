<?php

namespace App\Http\Controllers\Api\V1;

use Chaos\Module\Common\LaravelRestController;
use Chaos\Module\Dashboard\Service\DashboardService;
use Chaos\Module\Lookup\Service\LookupService;

/**
 * Class LookupController
 * @author ntd1712
 */
class LookupController extends LaravelRestController
{
    /**
     * Constructor.
     *
     * @param   LookupService $lookupService
     * @param   DashboardService $dashboardService
     * @throws  \Exception
     */
    public function __construct(LookupService $lookupService, DashboardService $dashboardService)
    {
        parent::__construct($lookupService, $dashboardService);
        $this->service = $lookupService;
    }

    /**
     * @throws  \Exception
     */
    public function index()
    {
        var_dump(
            $this->service->filter('10/29/2014', 86399),
            $this->getFilterParams($this->getRequest(null, false)),
            $this->getPagerParams($this->getRequest(null, false)),
            $this->getRequest(),
            $this->service,
            json_encode($this->service->getVars()->getContent()),
            serialize($this->getContainer()->get(M1_VARS)),
            serialize($this->getContainer()->get(DOCTRINE_ENTITY_MANAGER)),
            $this->getContainer()->get('Chaos\Module\Dashboard\Service\DashboardService'),
            $this->getContainer()->get('Chaos\Module\Lookup\Service\LookupService')
        );

        return [
            'data' => __FUNCTION__
        ];
    }
}
