<?php

namespace Shared\Foundation;

use Chaos\Bridge\Doctrine\EntityManagerFactory;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Ramsey\Uuid\Uuid;

/**
 * Class AbstractLaravelController
 * @author ntd1712
 */
abstract class AbstractLaravelController extends Controller
{
    use Traits\ConfigAwareTrait, Traits\ContainerAwareTrait, Traits\ServiceAwareTrait,
        BaseControllerTrait, DispatchesCommands, ValidatesRequests;

    /**
     * Constructor.
     *
     * @param   \ArrayAccess|array $container An array holding the paths to the service files.
     * @param   \ArrayAccess|array $config An array holding the paths to the config files.
     * @throws  \Exception
     */
    public function __construct($container = [], $config = [])
    {
        $this->__setContainer($container)->__setConfig($config);
        $this->__getContainer()->set(
            DOCTRINE_ENTITY_MANAGER,
            (new EntityManagerFactory)->__invoke(null, null, $this->__getConfig()->getContent())
        );
    }

    /**
     * Either get a query value or all of the input and files.
     *
     * @param   null|string $key The request parameter key.
     * @param   mixed $default [optional] The default value.
     * @param   boolean $deep [optional] Is parameter deep in multidimensional array.
     * @return  array|mixed
     */
    protected function getRequest($key = null, $default = null, $deep = false)
    {
        $request = parent::getRouter()->getCurrentRequest();

        if (isset($key)) {
            return $request->get($key, $default, $deep);
        }

        if (null === $default) {
            $vars = $request->all();
            $vars['EditedAt'] = 'now';
            $vars['EditedBy'] = \Session::get('loggedName');
            $vars['IsDeleted'] = 'false';
            $vars['Uuid'] = Uuid::uuid4()->toString();
            $vars['ApplicationKey'] = $this->__getConfig()->get('framework.application_key');

            return $vars;
        }

        return $request->all();
    }
}
