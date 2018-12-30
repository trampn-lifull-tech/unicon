<?php

namespace Chaos\Module\Lookup\Service;

use Chaos\Service\Service;
use Chaos\Module\Lookup\Repository\LookupRepository;

/**
 * Class LookupService
 * @author ntd1712
 */
class LookupService extends Service
{
    /**
     * @param   LookupRepository $lookupRepository
     * @throws  \Exception
     */
    public function __construct(LookupRepository $lookupRepository)
    {
        parent::__construct($lookupRepository);
        $this->repository = $lookupRepository;
    }
}
