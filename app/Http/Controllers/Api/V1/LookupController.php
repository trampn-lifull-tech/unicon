<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\ApiController;
use Chaos\Module\Lookup\Service\LookupService;

/**
 * Class LookupController
 * @author ntd1712
 */
class LookupController extends ApiController
{
    /**
     * GET /api/v1/lookup
     *
     * @param   LookupService $lookupService
     * @throws  \Exception
     */
    public function __construct(LookupService $lookupService)
    {
        parent::__construct($this->service = $lookupService);
    }
}
