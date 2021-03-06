<?php

namespace Chaos\Repository;

use Chaos\Support\Config\Contract\ConfigAware;
use Chaos\Support\Container\Contract\ContainerAware;
use Chaos\Support\Object\Contract\ObjectTrait;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Interop\Container\ContainerInterface;

/**
 * Class DoctrineRepository
 * @author ntd1712
 */
abstract class DoctrineRepository extends EntityRepository implements Contract\DoctrineRepositoryInterface
{
    use ContainerAware, ConfigAware,
        ObjectTrait, Contract\DoctrineRepositoryTrait;

    /**
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct()
    {
        //
    }

    /**
     * {@inheritdoc}
     *
     * @param   \Interop\Container\ContainerInterface $container The container object.
     * @param   object $instance [optional]
     * @return  static
     * @throws
     */
    public function __invoke(ContainerInterface $container, $instance = null)
    {
        $this->setContainer($container);
        $this->setVars($instance ?? $container->get('config'));

        $container = $this->getContainer();
        $container->set($this->getClass(), $this);

        $this->_em = $container->get('entity_manager');
        $this->_class = $this->_em->getClassMetadata($this->_entityName);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @param   array $paging The paging criteria.
     * @param   bool $fetchJoinCollection [optional] Whether the query joins a collection (true by default).
     * @return  \Doctrine\ORM\Tools\Pagination\Paginator
     * @throws  \Doctrine\ORM\ORMException
     */
    public function paginate($criteria = [], array $paging = [], $fetchJoinCollection = true)
    {
        $query = $this->getQueryBuilder($criteria);

        if (null === $query->getFirstResult()) {
            $query->setFirstResult($paging['CurrentPageStart'] ?? 0);
        }

        if (null === $query->getMaxResults()) {
            $query->setMaxResults($paging['ItemCountPerPage'] ?? CHAOS_QUERY_LIMIT);
        }

        return new Paginator($query, $fetchJoinCollection);
    }

    /**
     * {@inheritdoc}
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @param   null|string|int $hydrationMode [optional] Processing mode to be used during the hydration process.
     * @return  \ArrayIterator
     * @throws  \Doctrine\ORM\ORMException
     */
    public function readAll($criteria = [], $hydrationMode = AbstractQuery::HYDRATE_OBJECT)
    {
        $result = $this->getQueryBuilder($criteria)
            ->getQuery()
            ->execute(null, $hydrationMode);

        return new \ArrayIterator($result);
    }

    /**
     * {@inheritdoc}
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @param   null|string|int $hydrationMode [optional] The hydration mode.
     * @return  object
     * @throws  \Doctrine\ORM\ORMException
     */
    public function read($criteria, $hydrationMode = null)
    {
        $result = $this->getQueryBuilder($criteria)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult($hydrationMode);

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @param   object[]|object $entity The entity object.
     * @param   bool $autoFlush [optional]
     * @return  int The affected rows.
     * @throws  \Doctrine\ORM\ORMException
     */
    public function create($entity, $autoFlush = true)
    {
        return $this->update($entity, null, $autoFlush, true);
    }

    /**
     * {@inheritdoc}
     *
     * @param   object[]|object $entity The entity object.
     * @param   null|\Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @param   bool $autoFlush [optional]
     * @param   bool $isNew [optional] A flag indicating we are creating or updating a record.
     * @return  int The affected rows.
     * @throws  \Doctrine\ORM\ORMException
     */
    public function update($entity, $criteria = null, $autoFlush = true, $isNew = false)
    {
        if (isset($criteria)) {
            $entity = $this->read($criteria);
        }

        if (!is_array($entity)) {
            $entity = [$entity];
        }

        $count = 0;

        foreach ($entity as $v) {
            $isNew ? $this->_em->persist($v) : $v = $this->_em->merge($v);

            if ((0 === ++$count % CHAOS_QUERY_LIMIT) && $autoFlush) {
                $this->_em->flush();
            }
        }

        if ($autoFlush && 0 !== $count) {
            $this->_em->flush();
        }

        return $count;
    }

    /**
     * {@inheritdoc}
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array|object $criteria The criteria.
     * @param   bool $autoFlush [optional]
     * @return  int The affected rows.
     * @throws  \Doctrine\ORM\ORMException
     */
    public function delete($criteria, $autoFlush = true)
    {
        $entity = is_object($criteria) ? [$criteria] : $this->getQueryBuilder($criteria)->getQuery()->getResult();
        $count = 0;

        foreach ($entity as $v) {
            if ($this->_em->contains($v)) {
                $this->_em->remove($v);

                if ((0 === ++$count % CHAOS_QUERY_LIMIT) && $autoFlush) {
                    $this->_em->flush();
                }
            }
        }

        if ($autoFlush && 0 !== $count) {
            $this->_em->flush();
        }

        return $count;
    }

    /**
     * {@inheritdoc}
     *
     * @param   mixed|\Doctrine\Common\Collections\Criteria|array $criteria Either a query criteria or a field value.
     * @param   null|string $fieldName The field name; defaults to the identifier/primary key.
     * @return  bool
     */
    public function exist($criteria, $fieldName = null)
    {
        if (is_scalar($criteria)) {
            if (isset($fieldName)) {
                $criteria = [$fieldName => $criteria];
            } else if (!empty($this->_class->identifier)) {
                $instance = Criteria::create();

                foreach ($this->_class->identifier as $v) {
                    $instance->orWhere($instance->expr()->eq($v, $criteria));
                }

                $criteria = $instance;
            }
        }

        if ($criteria instanceof Criteria) {
            return 0 !== count($this->matching($criteria));
        }

        return null !== $this->findOneBy($criteria);
    }

    // <editor-fold desc="Transactional methods" defaultstate="collapsed">

    /**
     * @return  static
     */
    public function beginTransaction()
    {
        $this->_em->beginTransaction();

        return $this;
    }

    /**
     * @return  static
     * @throws  \Doctrine\DBAL\ConnectionException
     */
    public function commit()
    {
        if ($this->_em->getConnection()->isTransactionActive() && !$this->_em->getConnection()->isRollbackOnly()) {
            $this->_em->commit();
        }

        return $this;
    }

    /**
     * @return  static
     */
    public function rollback()
    {
        if ($this->_em->getConnection()->isTransactionActive()) {
            $this->_em->rollBack();
        }

        return $this;
    }

    /**
     * @param   null|object|array $entity The entity.
     * @return  static
     * @throws  \Doctrine\ORM\ORMException
     */
    public function flush($entity = null)
    {
        if ($this->_em->isOpen()) {
            $this->_em->flush($entity);
        }

        return $this;
    }

    /**
     * @return  static
     */
    public function close()
    {
        $this->_em->close();

        return $this;
    }

    // </editor-fold>

    // <editor-fold desc="Magic methods" defaultstate="collapsed">

    /**
     * @param   string $name The name of the property being interacted with.
     * @return  mixed
     */
    public function __get($name)
    {
        switch ($name) {
            case 'classname':
                return $this->_class->reflClass->getShortName();
            case 'entity':
                return new $this->_entityName;
            case 'fieldMappings':
                return $this->_class->fieldMappings;
            case 'identifier':
                return $this->_class->identifier;
            case 'criteria':
                return Criteria::create();
            case 'classMetadata':
                return $this->_class;
            case 'entityManager':
                return $this->_em;
            default:
                throw new \InvalidArgumentException('Invalid magic property on repository');
        }
    }

    // </editor-fold>
}
