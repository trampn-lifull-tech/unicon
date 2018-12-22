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
     * @param   LookupService $lookupService
     * @throws  \Exception
     */
    public function __construct(LookupService $lookupService)
    {
        parent::__construct($lookupService);
        $this->service = $lookupService;
    }
}
