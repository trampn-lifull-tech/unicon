<?php

namespace Chaos\Common\Application;

use Chaos\Common\Service\Contract\ServiceAware;
use Chaos\Common\Service\Contract\ServiceTrait;
use Chaos\Common\Support\Contract\ConfigAware;
use Chaos\Common\Support\Contract\ContainerAware;
use Chaos\Common\Support\Doctrine\EntityManagerFactory;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;

/**
 * Class LaravelRestController
 * @author ntd1712
 */
abstract class LaravelRestController extends Controller
{
    use ConfigAware, ContainerAware, ServiceAware,
        Contract\ControllerTrait, ServiceTrait;

    /**
     * Constructor.
     *
     * @param   array $container An array holding the paths to the service files.
     * @param   array $config An array holding the paths to the config files.
     * @throws  \Exception
     */
    public function __construct($container = [], $config = [])
    {
        $this->setContainer($container)->setVars($config);
        $this->getContainer()->set(VARS, $this->getVars());
        $this->getContainer()->set(
            ENTITY_MANAGER,
            (new EntityManagerFactory)->__invoke(null, null, $this->getVars()->getContent())
        );
    }

    /**
     * GET /lookup?filter=&sort=&start=&length=
     *
     * Displays a listing of the resource.
     * This is the default `index` action, you can override this in the derived class.
     *
     * @return  array|\Illuminate\Http\Response
     * @throws  \ReflectionException
     */
    public function index()
    {
        return $this->service->readAll($this->getFilterParams(), $this->getPagerParams());
    }

    /**
     * POST /lookup
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
     * GET /lookup/{lookup}
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
     * PUT /lookup/{lookup}
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
     * DELETE /lookup/{lookup}
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

    // <editor-fold desc="Private methods" defaultstate="collapsed">

    /**
     * Either gets a query value or all of the input and files.
     *
     * @param   null|string $key The request parameter key.
     * @param   mixed $default [optional] The default value.
     * @param   \Illuminate\Http\Request $request [optional]
     * @return  array|mixed
     */
    protected function getRequest($key = null, $default = null, $request = null)
    {
        if (empty($request)) {
            $request = request();
        }

        if (isset($key)) {
            return $request->get($key, $default);
        }

        $params = $request->all();

        if (null === $default) {
            $params['CreatedAt'] = 'now';
            $params['CreatedBy'] = session('loggedName');
            $params['NotUse'] = 'false';
            $params['ApplicationKey'] = $this->getVars()->get('app.key');

            try {
                $params['Guid'] = Uuid::uuid4()->toString();
            } catch (\Exception $ex) {
                //
            }

            return $params;
        }

        return $params;
    }

    // </editor-fold>
}
