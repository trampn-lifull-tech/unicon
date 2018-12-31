<?php

namespace Chaos\Application;

/**
 * Class LaravelRestController
 * @author ntd1712
 */
abstract class LaravelRestController extends LaravelController
{
    /**
     * GET /api/v1/lookup?filter=&sort=&start=&length=
     *
     * Displays a listing of the resource.
     * This is the default `index` action, you can override this in the derived class.
     *
     * @return  array|\Illuminate\Http\Response
     */
    public function index()
    {
        return $this->service->readAll(
            $this->getFilterParams($request = $this->getRequest(null, false), $this->service->repository->fields),
            $this->getPagerParams($request)
        );
    }

    /**
     * POST /api/v1/lookup
     *
     * Stores a newly created resource in storage.
     * This is the default `store` action, you can override this in the derived class.
     *
     * @param   \Illuminate\Http\Request $request
     * @return  array|\Illuminate\Http\Response
     */
    public function store($request)
    {
        return $this->service->create($this->getRequest(null, null, $request));
    }

    /**
     * GET /api/v1/lookup/:id
     *
     * Displays the specified resource.
     * This is the default `show` action, you can override this in the derived class.
     *
     * @param   mixed $id The route parameter ID.
     * @return  array|\Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->service->read($id);
    }

    /**
     * PUT /api/v1/lookup/:id
     *
     * Updates the specified resource in storage.
     * This is the default `update` action, you can override this in the derived class.
     *
     * @param   \Illuminate\Http\Request $request
     * @param   mixed $id The route parameter ID.
     * @return  array|\Illuminate\Http\Response
     */
    public function update($request, $id)
    {
        return $this->service->update($this->getRequest(null, null, $request), $id);
    }

    /**
     * DELETE /api/v1/lookup/:id
     *
     * Removes the specified resource from storage.
     * This is the default `destroy` action, you can override this in the derived class.
     *
     * @param   mixed $id The route parameter ID.
     * @return  array|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->service->delete($id);
    }
}