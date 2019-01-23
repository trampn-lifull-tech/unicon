<?php

namespace Chaos\Repository\Contract;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\AbstractQuery;

/**
 * Interface DoctrineRepositoryInterface
 * @author ntd1712
 *
 * @property-read string $classname The short class name of the entity class, e.g. User
 * @property-read object $entity The entity object.
 * @property-read array $fieldMappings The field mappings of the entity class.
 * @property-read array $identifier The field names that are part of the identifier/primary key of the entity class.
 * @property-read \Doctrine\Common\Collections\Criteria $criteria The <tt>Criteria</tt> instance.
 * @property-read \Doctrine\ORM\Mapping\ClassMetadata $classMetadata The <tt>ClassMetadata</tt> instance.
 * @property-read \Doctrine\ORM\EntityManager $entityManager The <tt>EntityManager</tt> instance.
 *
 * @method self beginTransaction() Starts a transaction by suspending auto-commit mode.
 * @method self commit() Commits the current transaction.
 * @method self rollback() Cancels any database changes done during the current transaction.
 * @method self flush($entity = null) Flushes all changes to objects that have been queued up to now to the database.
 * @method self close() Closes the connection.
 */
interface DoctrineRepositoryInterface extends RepositoryInterface, ObjectRepository
{
    /**
     * The default `paginate` method, you can override this in the derived class.
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @param   array $paging The paging criteria.
     * @param   bool $fetchJoinCollection [optional] Whether the query joins a collection (true by default).
     * @return  \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function paginate($criteria = [], array $paging = [], $fetchJoinCollection = true);

    /**
     * The default `readAll` method, you can override this in the derived class.
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @param   null|string|int $hydrationMode [optional] Processing mode to be used during the hydration process.
     * @return  \ArrayIterator
     */
    public function readAll($criteria = [], $hydrationMode = AbstractQuery::HYDRATE_OBJECT);

    /**
     * The default `read` method, you can override this in the derived class.
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @param   null|string|int $hydrationMode [optional] The hydration mode.
     * @return  object
     */
    public function read($criteria, $hydrationMode = null);

    /**
     * The default `create` method, you can override this in the derived class.
     *
     * @param   object[]|object $entity The entity object.
     * @param   bool $autoFlush [optional]
     * @return  int The affected rows.
     */
    public function create($entity, $autoFlush = true);

    /**
     * The default `update` method, you can override this in the derived class.
     *
     * @param   object[]|object $entity The entity object.
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
     * @param   null|string $fieldName The field name; defaults to the identifier/primary key.
     * @return  bool
     */
    public function exist($criteria, $fieldName = null);
}
