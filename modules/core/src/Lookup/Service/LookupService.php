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
}
