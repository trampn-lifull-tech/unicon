<?php

namespace Chaos\Shared\Foundation;

use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;
use Chaos\Factory\Doctrine\EntityManagerFactory;

/**
 * Class LaravelController
 * @author ntd1712
 */
abstract class LaravelController extends Controller
{
    use ConfigAwareTrait, ContainerAwareTrait;
        /*Traits\ServiceAwareTrait, BaseControllerTrait;*/

    /**
     * Constructor.
     *
     * @param   \ArrayAccess|array $container An array holding the paths to the service files.
     * @param   \ArrayAccess|array $config An array holding the paths to the config files.
     * @throws  \Exception
     */
    public function __construct($container = [], $config = [])
    {
        $this->setContainer($container)->setVars($config);
        $this->getContainer()->set(
            DOCTRINE_ENTITY_MANAGER,
            (new EntityManagerFactory)->__invoke(null, null, $this->getVars()->getContent())
        );
    }

    /**
     * Either get a query value or all of the input and files.
     *
     * @param   null|string $key The request parameter key.
     * @param   mixed $default [optional] The default value.
     * @return  array|mixed
     * @throws  \Exception
     */
    protected function getRequest($key = null, $default = null)
    {
        if (isset($key)) {
            return request($key, $default);
        }

        $params = request()->all();

        if (null === $default) {
            $params['EditedAt'] = 'now';
            $params['EditedBy'] = session('loggedName');
            $params['IsDeleted'] = 'false';
            $params['Uuid'] = Uuid::uuid4()->toString();
            $params['ApplicationKey'] = $this->getVars()->get('framework.application_key');

            return $params;
        }

        return $params;
    }
}
