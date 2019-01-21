<?php

namespace Chaos\Repository\Contract;

use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Interface DoctrineRepositoryInterface
 * @author ntd1712
 *
 * @property-read string $className The short class name of the entity class, e.g. User
 * @property-read string $entityName The qualified class name of the entity class, e.g. Entities\User
 * @property-read object $entity The entity object.
 * @property-read array $fieldMappings The field mappings of the entity class.
 * @property-read array $identifier The field names that are part of the identifier/primary key of the entity class.
 * @property-read \Doctrine\Common\Collections\Criteria $criteria The <tt>Criteria</tt> instance.
 * @property-read \Doctrine\ORM\Mapping\ClassMetadata $classMetadata The <tt>ClassMetadata</tt> instance.
 * @property-read \Doctrine\ORM\EntityManager $entityManager The <tt>EntityManager</tt> instance.
 *
 * @property bool $enableTransaction A value that indicates whether the transaction is enabled.
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
     * @param   null|string $fieldName The field name; defaults to Primary Key.
     * @return  bool
     */
    public function exist($criteria, $fieldName = null);
}
