<?php

namespace Chaos\Service\Contract;

use Chaos\Support\Config\Contract\VarsAwareInterface;
use Chaos\Support\Container\Contract\ContainerAwareInterface;
use Chaos\Support\Container\Contract\InitializerInterface;
use Chaos\Support\Object\Contract\ObjectInterface;

/**
 * Interface ServiceHandlerInterface
 * @author ntd1712
 *
 * @property \Chaos\Repository\Contract\RepositoryInterface $repository
 */
interface ServiceHandlerInterface extends
    ObjectInterface,
    InitializerInterface,
    ContainerAwareInterface,
    VarsAwareInterface
{
    /**
     * The default `readAll` method, you can override this in the derived class.
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @param   bool|array $paging The paging criteria; defaults to FALSE.
     * @return  array
     */
    public function readAll($criteria = [], $paging = false);

    /**
     * The default `read` method, you can override this in the derived class.
     *
     * @param   mixed|\Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @return  array
     */
    public function read($criteria);

    /**
     * The default `create` method, you can override this in the derived class.
     *
     * @param   array $post The _POST variable.
     * @return  array
     */
    public function create(array $post = []);

    /**
     * The default `update` method, you can override this in the derived class.
     *
     * @param   array $post The _PUT variable.
     * @param   mixed|\Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @return  array
     */
    public function update(array $post = [], $criteria = null);

    /**
     * The default `delete` method, you can override this in the derived class.
     *
     * @param   mixed|\Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @return  array
     */
    public function delete($criteria);
}
