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
     * @param   LookupService $lookupService
     * @param   DashboardService $dashboardService
     */
    public function __construct(LookupService $lookupService, DashboardService $dashboardService)
    {
        parent::__construct($this->service = $lookupService, $this->dashboardService = $dashboardService);
    }

    /**
     * For testing purpose only.
     *
     * @throws  \Exception
     */
    public function index()
    {
        echo '<pre>';
        var_dump(
            $this->getRequest(),
            $this->getOrderParams(
                $request = $this->getRequest(null, false),
                $this->getFilterParams($request)
            ),
            $this->getPagerParams($request),

            $this->filter('1/39/2019', true),
            $this->filter('1/29/2019', true),
            $this->filter('1/29/2019', 86399)
        );

        var_dump(
            $this->service,
            $this->dashboardService === ($dashboardService = $this->service->test()),
            $this->dashboardService->getVars() === $dashboardService->getVars()
        );

        $this->service->repository->getVars()->set('test_config', __FUNCTION__);
        var_export($this->getContainer()->get('Chaos\Module\Dashboard\Service\DashboardService')->getVars()->getContent());
        echo '<br><br>';

        return [
            'data' => __METHOD__
        ];
    }

    private $dashboardService;
}
