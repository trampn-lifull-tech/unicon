<?php

namespace Chaos\Repository\Contract;

use Chaos\Support\Config\Contract\VarsAwareInterface;
use Chaos\Support\Container\Contract\ContainerAwareInterface;
use Chaos\Support\Object\Contract\ObjectInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;

/**
 * Interface RepositoryInterface
 * @author ntd1712
 *
 * @property-read string $className The short class name of the entity, e.g. User
 * @property-read string $entityName The qualified class name of the entity, e.g. Entities\User
 * @property-read object $entity The entity instance.
 * @property-read array $fields The field mappings of the entity.
 * @property-read array $pk The field names that are part of the identifier/primary key of the entity.
 * @property-read \Doctrine\Common\Collections\Criteria $criteria The <tt>Criteria</tt> instance.
 * @property bool $enableTransaction A value that indicates whether the transaction is enabled.
 *
 * @method string getClassName() Returns the class name of the object managed by the repository, e.g. Entities\User
 * @method RepositoryInterface beginTransaction() Starts a transaction by suspending auto-commit mode.
 * @method RepositoryInterface commit() Commits the current transaction.
 * @method RepositoryInterface rollback() Cancels any database changes done during the current transaction.
 * @method RepositoryInterface flush() Flushes all changes to objects that have been queued up to now to the database.
 * @method RepositoryInterface close() Closes the connection.
 */
interface RepositoryInterface extends
    ContainerAwareInterface,
    VarsAwareInterface,
    ObjectInterface,
    InitializerInterface
{
    /**
     * The default `paginate` method, you can override this in the derived class.
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @param   array $paging The paging criteria.
     * @return  \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function paginate($criteria = [], array $paging = []);

    /**
     * The default `readAll` method, you can override this in the derived class.
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @return  \ArrayIterator
     */
    public function readAll($criteria = []);

    /**
     * The default `read` method, you can override this in the derived class.
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @return  object
     */
    public function read($criteria);

    /**
     * The default `create` method, you can override this in the derived class.
     *
     * @param   object[]|object $entity The entity instance.
     * @param   bool $autoFlush [optional]
     * @return  int The affected rows.
     */
    public function create($entity, $autoFlush = true);

    /**
     * The default `update` method, you can override this in the derived class.
     *
     * @param   object[]|object $entity The entity instance.
     * @param   null|\Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @param   bool $autoFlush [optional]
     * @return  int The affected rows.
     */
    public function update($entity, $criteria = null, $autoFlush = true);

    /**
     * The default `delete` method, you can override this in the derived class.
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array|object $criteria The criteria.
     * @param   bool $autoFlush [optional]
     * @return  int The affected rows.
     */
    public function delete($criteria, $autoFlush = true);

    /**
     * The default `exist` method, you can override this in the derived class.
     *
     * @param   mixed|\Doctrine\Common\Collections\Criteria|array $criteria Either a query criteria or a field value.
     * @param   null|string $fieldName The field name; defaults to Primary Key.
     * @return  bool
     */
    public function exist($criteria, $fieldName = null);
}
