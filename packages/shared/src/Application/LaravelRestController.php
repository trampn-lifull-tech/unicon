<?php

namespace Chaos\Shared\Application;

/**
 * Class LaravelRestController
 * @author ntd1712
 */
abstract class LaravelRestController extends LaravelController
{
    /**
     * Display a listing of the resource.
     *
     * This is the default `index` action, you can override this in the derived class.
     * GET /lookup?filter=&sort=&start=&length=
     *
     * @return  array|\Illuminate\Http\Response
     */
    public function index()
    {
//        return $this->getService()->readAll($this->getFilterParams(), $this->getPagerParams());
    }

    /**
     * Store a newly created resource in storage.
     *
     * This is the default `store` action, you can override this in the derived class.
     * POST /lookup
     *
     * @param   \Illuminate\Http\Request $request
     * @return  array|\Illuminate\Http\Response
     */
    public function store($request)
    {
//        return $this->getService()->create($this->getRequest());
    }

    /**
     * Display the specified resource.
     *
     * This is the default `show` action, you can override this in the derived class.
     * GET /lookup/{lookup}
     *
     * @param   mixed $id The route parameter ID.
     * @return  array|\Illuminate\Http\Response
     */
    public function show($id)
    {
//        return $this->getService()->read($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * This is the default `update` action, you can override this in the derived class.
     * PUT /lookup/{lookup}
     *
     * @param  \Illuminate\Http\Request $request
     * @param   mixed $id The route parameter ID.
     * @return  array|\Illuminate\Http\Response
     */
    public function update($request, $id)
    {
//        return $this->getService()->update($this->getRequest(), $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * This is the default `destroy` action, you can override this in the derived class.
     * DELETE /lookup/{lookup}
     *
     * @param   mixed $id The route parameter ID.
     * @return  array|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
//        return $this->getService()->delete($id);
    }
}
