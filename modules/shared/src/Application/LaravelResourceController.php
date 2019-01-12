<?php

namespace Chaos\Application;

/**
 * Class LaravelResourceController
 * @author ntd1712
 */
abstract class LaravelResourceController extends LaravelController
{
    /**
     * GET /api/v1/demo?filter=ntd1712&sort=name&direction=desc&nulls=first&start=0&length=10
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
     * GET /api/v1/demo/create
     *
     * Shows the form for creating a new resource.
     * The default `create` action, you can override this in the derived class.
     *
     * @return  array|\Illuminate\Http\Response
     */
    public function create()
    {
        return ['TODO'];
    }

    /**
     * POST /api/v1/demo
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
     * GET /api/v1/demo/:id
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
     * GET /api/v1/demo/:id/edit
     *
     * Shows the form for editing the specified resource.
     * The default `edit` action, you can override this in the derived class.
     *
     * @param   mixed $id The route parameter ID.
     * @return  array|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        return ["TODO: $id"];
    }

    /**
     * PUT /api/v1/demo/:id
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
     * DELETE /api/v1/demo/:id
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
