<?php

namespace Chaos\Common\Repository\Contract;

/**
 * Interface IRepository
 * @author ntd1712
 *
 * @property-read string $className The short class name of the entity, e.g. User
 * @property-read string $entityName The qualified class name of the entity, e.g. Entities\User
 * @property-read \Chaos\Common\Repository\Contract\IEntity $entity The entity instance.
 * @property-read array $fields The field mappings of the entity.
 * @property-read array $pk The field names that are part of the identifier/primary key of the entity.
 *
 * @method self beginTransaction() Starts a transaction by suspending auto-commit mode.
 * @method self commit() Commits the current transaction.
 * @method self rollback() Cancels any database changes done during the current transaction.
 * @method self flush() Flushes all changes to objects that have been queued up to now to the database.
 * @method self close() Closes the connection.
 * @method string getClassName() Returns the class name of the object managed by the repository, e.g. Entities\User
 */
interface IRepository
{
    /**
     * The default `paginate` method, you can override this in the derived class.
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @param   array $paging The paging criteria.
     * @return  \Doctrine\ORM\Tools\Pagination\Paginator
     * @throws  \Doctrine\ORM\ORMException
     * @throws  \InvalidArgumentException
     * @throws  \ReflectionException
     */
    public function paginate($criteria = [], array $paging = []);

    /**
     * The default `readAll` method, you can override this in the derived class.
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @return  \ArrayIterator
     * @throws  \Doctrine\ORM\ORMException
     * @throws  \InvalidArgumentException
     * @throws  \ReflectionException
     */
    public function readAll($criteria = []);

    /**
     * The default `read` method, you can override this in the derived class.
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @return  object
     * @throws  \Doctrine\ORM\ORMException
     * @throws  \InvalidArgumentException
     * @throws  \ReflectionException
     */
    public function read($criteria);

    /**
     * The default `create` method, you can override this in the derived class.
     *
     * @param   object[]|object $entity The entity instance.
     * @return  int The affected rows.
     * @throws  \Doctrine\ORM\ORMException
     * @throws  \InvalidArgumentException
     * @throws  \ReflectionException
     */
    public function create($entity);

    /**
     * The default `update` method, you can override this in the derived class.
     *
     * @param   object[]|object $entity The entity instance.
     * @param   null|\Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @return  int The affected rows.
     * @throws  \Doctrine\ORM\ORMException
     * @throws  \InvalidArgumentException
     * @throws  \ReflectionException
     */
    public function update($entity, $criteria = null);

    /**
     * The default `delete` method, you can override this in the derived class.
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array|object $criteria The criteria.
     * @return  int The affected rows.
     * @throws  \Doctrine\ORM\ORMException
     * @throws  \InvalidArgumentException
     * @throws  \ReflectionException
     */
    public function delete($criteria);

    /**
     * The default `exist` method, you can override this in the derived class.
     *
     * @param   \Doctrine\Common\Collections\Criteria|array|mixed $criteria Either a query criteria or a field value.
     * @param   null|string $fieldName The field name; defaults to `Id`.
     * @return  bool
     */
    public function exist($criteria, $fieldName = null);
}
