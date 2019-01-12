<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\ApiController;
use Chaos\Module\Dashboard\Service\DashboardService;
use Chaos\Module\Lookup\Service\LookupService;

/**
 * Class LookupController
 * @author ntd1712
 */
class LookupController extends ApiController
{
    /**
     * GET /api/v2/lookup
     *
     * @param   \Chaos\Module\Lookup\Service\LookupService $lookupService
     * @param   \Chaos\Module\Dashboard\Service\DashboardService $dashboardService
     */
    public function __construct(LookupService $lookupService, DashboardService $dashboardService)
    {
        parent::__construct($this->service = $lookupService, $dashboardService);
    }

    /**
     * For testing purpose only.
     *
     * @throws  \Exception
     */
    public function index()
    {
        var_dump(
            $this->getContainer()->get('Chaos\Module\Dashboard\Service\DashboardService'),
            $this->service,
//            $this->service->repository,

            $this->filter('15/39/2014', true),
            $this->filter('10/29/2014', true),
            $this->filter('10/29/2014', 86399),

            $this->getFilterParams($this->getRequest(null, false)),
            $this->getPagerParams($this->getRequest(null, false)),
            $this->getRequest(),

            json_encode($this->getContainer()->get(M1_VARS)->getContent())
//            serialize($this->getContainer()->get(DOCTRINE_ENTITY_MANAGER))
        );

        echo '<pre>';
        var_export($this->service->getVars()->getContent());
        echo '<br><br>';

        $this->service->getVars()->set('test_config', 'lookupService');
        var_export($this->getContainer()->get('Chaos\Module\Dashboard\Service\DashboardService')->getVars()->getContent());
        echo '<br><br>';

        return [
            'data' => __FUNCTION__
        ];
    }
}
