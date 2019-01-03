<?php

namespace Chaos\Service\Contract;

use Chaos\Support\Contract\IConfigAware;
use Chaos\Support\Contract\IContainerAware;
use Chaos\Support\Object\Contract\IObject;

/**
 * Interface IServiceHandler
 * @author ntd1712
 *
 * @property \Chaos\Repository\Contract\IRepository $repository
 */
interface IServiceHandler extends IConfigAware, IContainerAware, IObject
{
    /**
     * The default `readAll` method, you can override this in the derived class.
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @param   bool|array $paging The paging criteria; defaults to FALSE.
     * @return  array
     * @throws  \Doctrine\ORM\ORMException
     */
    public function readAll($criteria = [], $paging = false);

    /**
     * The default `read` method, you can override this in the derived class.
     *
     * @param   mixed|\Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @return  array
     * @throws  \Chaos\Service\Exception\ServiceException
     * @throws  \Doctrine\ORM\ORMException
     */
    public function read($criteria);

    /**
     * The default `create` method, you can override this in the derived class.
     *
     * @param   array $post The _POST variable.
     * @return  array
     * @throws  \Chaos\Service\Exception\ServiceException
     * @throws  \Doctrine\ORM\ORMException
     */
    public function create(array $post = []);

    /**
     * The default `update` method, you can override this in the derived class.
     *
     * @param   array $post The _PUT variable.
     * @param   mixed|\Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @return  array
     * @throws  \Chaos\Service\Exception\ServiceException
     * @throws  \Doctrine\ORM\ORMException
     */
    public function update(array $post = [], $criteria = null);

    /**
     * The default `delete` method, you can override this in the derived class.
     *
     * @param   mixed|\Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @return  array
     * @throws  \Chaos\Service\Exception\ServiceException
     * @throws  \Doctrine\ORM\ORMException
     */
    public function delete($criteria);
}
