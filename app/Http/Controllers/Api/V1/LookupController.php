<?php

namespace App\Http\Controllers\Api\V1;

use Chaos\Module\Common\LaravelRestController;
use Chaos\Core\Lookup\Service\LookupService;

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
        parent::__construct();

        $this->service = $service
            ->setContainer($this->getContainer())
            ->setVars($this->getVars());
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
            $this->getService('Chaos\Module\Dashboard\Service\DashboardService'),
            $this->getContainer()->get(M1_VARS)->getContent(),
            $this->getContainer()->get(DOCTRINE_ENTITY_MANAGER)
        );

        return ['data' => __FUNCTION__];
    }
}
