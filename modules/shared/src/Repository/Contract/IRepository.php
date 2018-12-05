<?php

namespace Chaos\Common\Repository\Contract;

/**
 * @todo
 *
 * Interface IBaseRepository
 * @author ntd1712
 *
 * @property-read string $className The short class name of the entity, e.g. User
 * @property-read string $entityName The qualified class name of the entity, e.g. Entities\User
 * @property-read \Chaos\Foundation\Contracts\IBaseEntity $entity The entity instance.
 * @property-read array $fields The field mappings of the entity.
 * @property-read array $pk The field names that are part of the identifier/primary key of the entity.
 *
 * @method self beginTransaction() Start a transaction by suspending auto-commit mode.
 * @method self commit() Commit the current transaction.
 * @method self rollback() Cancel any database changes done during the current transaction.
 * @method self flush() Flush all changes to objects.
 * @method self close() Close the EntityManager (if any).
 * @method string getClassName() Return the class name of the object managed by the repository, e.g. Entities\User
 */
interface IRepository
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
     * @return  integer The affected rows.
     */
    public function create($entity);

    /**
     * The default `update` method, you can override this in the derived class.
     *
     * @param   object[]|object $entity The entity instance.
     * @param   null|\Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @return  integer The affected rows.
     */
    public function update($entity, $criteria = null);

    /**
     * The default `delete` method, you can override this in the derived class.
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array|object $criteria The criteria.
     * @return  integer The affected rows.
     */
    public function delete($criteria);

    /**
     * The default `exist` method, you can override this in the derived class.
     *
     * @param   \Doctrine\Common\Collections\Criteria|array|mixed $criteria Either a query criteria or a field value.
     * @param   null|string $fieldName The field name; defaults to `Id`.
     * @return  boolean
     */
    public function exist($criteria, $fieldName = null);
}
