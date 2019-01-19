<?php

namespace Chaos\Repository;

use Chaos\Support\Config\Contract\VarsAware;
use Chaos\Support\Container\Contract\ContainerAware;
use Chaos\Support\Object\Contract\ObjectTrait;
use Chaos\Support\Orm\EntityManagerFactory;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Interop\Container\ContainerInterface;

/**
 * Class DoctrineRepository
 * @author ntd1712
 *
 * @property-read \Doctrine\ORM\EntityManager $entityManager The <tt>EntityManager</tt> instance.
 * @property-read \Doctrine\ORM\Mapping\ClassMetadata $metadata The <tt>ClassMetadata</tt> instance.
 *
 * @method string getClassName()
 * @method Contract\RepositoryInterface beginTransaction()
 * @method Contract\RepositoryInterface commit()
 * @method Contract\RepositoryInterface rollback()
 * @method Contract\RepositoryInterface flush()
 * @method Contract\RepositoryInterface close()
 */
abstract class DoctrineRepository /*extends EntityRepository*/ implements Contract\RepositoryInterface
{
    use ContainerAware, VarsAware,
        ObjectTrait, Contract\DoctrineRepositoryTrait;

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
        $container = $this->getContainer();

        $this->setVars($instance ?? $container->get('config'));
        $vars = $this->getVars();

        $this->_em = (new EntityManagerFactory)($container, null, $vars->getContent());
        $container->set('em', $this->_em);
        $container->set($this->getClass(), $this);

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
     * @param   int $hydrationMode [optional] Processing mode to be used during the hydration process.
     * @return  \ArrayIterator
     * @throws  \Doctrine\ORM\ORMException
     */
    public function readAll($criteria = [], $hydrationMode = AbstractQuery::HYDRATE_OBJECT)
    {
        $result = $this->getQueryBuilder($criteria)
            ->getQuery()->execute(null, $hydrationMode);

        return new \ArrayIterator($result);
    }

    /**
     * {@inheritdoc}
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @param   int $hydrationMode [optional] The hydration mode.
     * @return  object
     * @throws  \Doctrine\ORM\ORMException
     */
    public function read($criteria, $hydrationMode = null)
    {
        $result = $this->getQueryBuilder($criteria)
            ->setMaxResults(1)->getQuery()->getOneOrNullResult($hydrationMode);

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @param   object[]|object $entity The entity instance.
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
     * @param   object[]|object $entity The entity instance.
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
     * @param   null|string $fieldName The field name; defaults to Primary Key.
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

    // <editor-fold desc="Magic methods" defaultstate="collapsed">

    /**
     * @param   string $name The name of the property being interacted with.
     * @return  mixed
     */
    public function __get($name)
    {
        switch ($name) {
            case 'className':
                return $this->_class->reflClass->getShortName();
            case 'entityName':
                return $this->_entityName;
            case 'entity':
                return new $this->_entityName;
            case 'fields':
                return $this->_class->fieldMappings;
            case 'identifier':
                return $this->_class->identifier;
            case 'criteria':
                return Criteria::create();
            case 'entityManager':
                return $this->_em;
            case 'metadata':
                return $this->_class;
            default:
                throw new \InvalidArgumentException('Invalid magic property on repository');
        }
    }

    // </editor-fold>
}
