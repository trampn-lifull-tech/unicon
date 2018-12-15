<?php

namespace App\Http\Controllers\Api\V1;

use Chaos\Module\Common\LaravelRestController;
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
     * @param   LookupService $service
     * @throws  \Exception
     */
    public function __construct(LookupService $service)
    {
        parent::__construct($service);
    }

    /**
     * For testing purpose only.
     *
     * {@inheritdoc} @override
     * @throws  \Exception
     */
    public function index()
    {
        var_dump(
            $this->getRequest(),
            $this->service,
            $this->service->getVars()->getContent(),
//            $this->getService('Chaos\Module\Dashboard\Service\DashboardService'),
            $this->getContainer()->get(M1_VARS),
            $this->getContainer()->get(DOCTRINE_ENTITY_MANAGER),
            $this->getContainer()->get('Chaos\Module\Lookup\Service\LookupService')
        );

        return ['data' => __FUNCTION__];
    }
}
