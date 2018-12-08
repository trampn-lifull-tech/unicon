<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\ApiController;

/**
 * Class LookupController
 * @author ntd1712
 */
class LookupController extends ApiController
{
    /**
     * For testing purpose only.
     *
     * {@inheritdoc} @override
     */
    public function index()
    {
        return ['data' => __FUNCTION__];
    }
}
