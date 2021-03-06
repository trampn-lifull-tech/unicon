<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\ApiController;
use Chaos\Module\Lookup\Service\LookupService;
use Chaos\Module\Dashboard\Service\DashboardService;

/**
 * Class LookupController
 * @author ntd1712
 *
 * For testing purpose only.
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
        parent::__construct($this->service = $lookupService, $dashboardService);
    }

    public function index()
    {
        echo '<pre>';
        var_dump(
            app('Chaos\Module\Dashboard\Service\DashboardService'),
            $this->getContainer()->get('Chaos\Module\Dashboard\Service\DashboardService'),

            $this->getVars()->get('app.copyright'),
            $this->getRequest(),
            $this->getOrderParams(
                $request = $this->getRequest(null, false),
                $this->getFilterParams($request, $fieldMappings = $this->service->repository->fieldMappings)
            ),
            $this->getPagerParams($request),

            (string)$this->service->repository->getQueryBuilder(
                $this->getOrderParams(
                    $request = $this->getRequest(null, false),
                    $this->getFilterParams($request, $fieldMappings)
                )
            ),
            json_encode($fieldMappings),

            $this->filter('1/39/2019', true),
            $this->filter('1/29/2019', true),
            $this->filter('1/29/2019', 86399),

            $this->getContainer()->getServiceIds(),
            $this->service
        );

        $this->service->repository->getVars()->set('test_config', __FUNCTION__);
        var_export($this->getContainer()->get('Chaos\Module\Dashboard\Service\DashboardService')->getVars()->all());
        echo '<br><br>';

        return [
            'data' => __METHOD__
        ];
    }
}
