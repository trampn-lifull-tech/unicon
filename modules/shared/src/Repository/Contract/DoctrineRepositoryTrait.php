<?php

namespace Chaos\Repository\Contract;

use Chaos\Support\Constant\JoinType;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Select;

/**
 * Class DoctrineRepositoryTrait
 * @author ntd1712
 *
 * @property \Doctrine\ORM\EntityManager $_em
 */
trait DoctrineRepositoryTrait
{
    /**
     * @return  static
     */
    public function beginTransaction()
    {
        $this->_em->getConnection()->beginTransaction();

        return $this;
    }

    /**
     * @return  static
     * @throws  \Doctrine\DBAL\ConnectionException
     */
    public function commit()
    {
        if ($this->_em->getConnection()->isTransactionActive() && !$this->_em->getConnection()->isRollbackOnly()) {
            $this->_em->getConnection()->commit();
        }

        return $this;
    }

    /**
     * @return  static
     * @throws  \Doctrine\DBAL\ConnectionException
     */
    public function rollback()
    {
        if ($this->_em->getConnection()->isTransactionActive()) {
            $this->_em->getConnection()->rollBack();
        }

        return $this;
    }

    /**
     * @return  static
     * @throws  \Doctrine\ORM\ORMException
     */
    public function flush()
    {
        if ($this->_em->isOpen()) {
            $this->_em->flush();
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

    /**
     * Gets the <tt>QueryBuilder</tt> instance.
     *
     * @param   \Doctrine\ORM\QueryBuilder|\Doctrine\Common\Collections\Criteria|array $criteria The criteria.
     * @param   \Doctrine\ORM\QueryBuilder $queryBuilder [optional] The <tt>QueryBuilder</tt> instance.
     * @return  \Doctrine\ORM\QueryBuilder
     * @throws  \Doctrine\ORM\ORMException
     */
    protected function getQueryBuilder($criteria, QueryBuilder $queryBuilder = null)
    {
        if ($criteria instanceof QueryBuilder) {
            return $criteria;
        }

        if (null === $queryBuilder) {
            $queryBuilder = $this->createQueryBuilder($this->_class->reflClass->getShortName());
        }

        if ($criteria instanceof Criteria) {
            return $queryBuilder->addCriteria($criteria);
        }

        if (empty($criteria) || !is_array($criteria)) {
            return $queryBuilder;
        }

        // switch...
        foreach ($criteria as $k => $v) {
            if (empty($v)) {
                continue;
            }

            $aliases = $queryBuilder->getAllAliases();

            switch ($k) {
                case Select::TABLE:
                case 'from':
                    // e.g. ['from' => $this->getRepository('User')]
                    //      ['from' => 'User u INDEX BY u.Id, Role r, Permission']
                    //      ['from' => ['from' => 'User', 'alias' => 'u', 'indexBy' => 'u.Id']]
                    //      ['from' => [
                    //          ['from' => 'User', 'alias' => 'u', 'indexBy' => 'u.Id'],
                    //          ['from' => 'Role', 'alias' => 'r'],
                    //          ['from' => $this->permissionRepository]
                    //      ]]
                    if ($v instanceof IRepository) {
                        $v = [
                            ['from' => $v->getClassName()]
                        ];
                    } else if (is_string($v)) {
                        $matches = preg_split(CHAOS_REPLACE_COMMA_SEPARATOR, $v, -1, PREG_SPLIT_NO_EMPTY);
                        $v = [];

                        foreach ($matches as $m) {
                            $parts = preg_split(CHAOS_REPLACE_SPACE_SEPARATOR, $m, -1, PREG_SPLIT_NO_EMPTY);
                            $v[] = ['from' => $parts[0], 'alias' => @$parts[1], 'indexBy' => @$parts[4]];
                        }
                    } else if (!is_array($v)) {
                        throw new \InvalidArgumentException(__METHOD__ . " expects '$k' in array format");
                    }

                    if (!isset($v[0])) { // make sure we have a multidimensional array passed
                        $v = [$v];
                    }

                    foreach ($v as $from) {
                        if (!is_array($from) || empty($from['from'])) {
                            throw new \InvalidArgumentException(
                                __METHOD__ . " expects '$k' in array format and its required key 'from'"
                            );
                        }

                        if ($from['from'] instanceof IRepository) {
                            $from['from'] = $from['from']->getClassName();
                        }

                        if (false === strpos($from['from'], '\\')) {
                            $from['from'] = guessNs($from['from'], $this->_class->namespace);
                        }

                        if (in_array($from['from'], $queryBuilder->getRootEntities(), true)) {
                            continue;
                        }

                        if (!isset($from['alias'])) {
                            $from['alias'] = shorten($from['from']);
                        }

                        if (!isset($from['indexBy'])) {
                            $from['indexBy'] = null;
                        }

                        $queryBuilder->from(trim($from['from']), trim($from['alias']), $from['indexBy']);
                    }
                    break;
                case Select::COLUMNS:
                case Select::SELECT:
                    // e.g. ['select' => '*']
                    //      ['select' => $this->getRepository('User')]
                    //      ['select' => 'User, Role']
                    //      ['select' => ['User', 'Role']
                    //      ['select' => ['User.Id', 'Role.Id']
                    //      ['select' => [
                    //          $this->getRepository('User'),
                    //          $this->roleRepository
                    //      ]
                    if ($v instanceof IRepository) {
                        $v = [$v->className];
                    } else if (is_string($v)) {
                        $v = preg_split(CHAOS_REPLACE_COMMA_SEPARATOR, $v, -1, PREG_SPLIT_NO_EMPTY);
                    } else if (!is_array($v)) {
                        throw new \InvalidArgumentException(__METHOD__ . " expects '$k' in array format");
                    }

                    foreach ($v as $select) {
                        if (empty($select) || Select::SQL_STAR === $select) {
                            continue;
                        }

                        if ($select instanceof IRepository) {
                            $select = $select->className;
                        }

                        $queryBuilder->addSelect($select);
                    }

                    // check if "select" array has duplicates
                    $parts = array_unique($queryBuilder->getDQLPart('select'));
                    $queryBuilder->resetDQLPart('select');

                    foreach ($parts as $select) {
                        $queryBuilder->add('select', $select, true);
                    }
                    break;
                case 'distinct':
                    // e.g. ['distinct' => true]
                    $queryBuilder->distinct();
                    break;
                case Select::QUANTIFIER:
                    // e.g. ['quantifier' => 'distinct']
                    $queryBuilder->distinct(Select::QUANTIFIER_DISTINCT === strtoupper($v));
                    break;
                case Select::JOINS:
                case 'join':
                    // e.g. ['joins' => [ // User INNER JOIN UserRole WITH UserRole = User.UserRole
                    //          ['join' => $this->userRoleRepository, 'condition' => '%1$s = %2$s.%1$s'],
                    //          ['join' => $this->roleRepository, 'condition' => '%3$s = %2$s.%3$s']
                    //      ]]            // ...  INNER JOIN Role WITH Role = UserRole.Role
                    //      ['joins' => ['innerJoin' => $this->userRoleRepository]]
                    //      ['joins' => ['leftJoin' => $this->getRepository('User'), 'condition' => '%3$s = %2$s.%3$s']]
                    if (!is_array($v)) {
                        throw new \InvalidArgumentException(__METHOD__ . " expects '$k' in array format");
                    }

                    if (!isset($v[0])) { // must be a multidimensional array!
                        $v = [$v];
                    }

                    foreach ($v as $join) {
                        if (!is_array($join) || !JoinType::has($type = key($join))) {
                            throw new \InvalidArgumentException(
                                __METHOD__ . " expects '$k' in array format" . ' and its required key "join"'
                            );
                        }

                        if ($join[$type] instanceof IRepository) {
                            $join[$type] = $join[$type]->getClassName();
                            // $join['alias'] = $join[$type]->className;
                        }

                        if (!isset($join['alias'])) {
                            $join['alias'] = shorten($join[$type]);
                        }

                        if (!isset($join['conditionType'])
                            || !in_array(strtoupper($join['conditionType']), [Join::ON, Join::WITH], true)
                        ) {
                            $join['conditionType'] = Join::WITH;
                        }

                        $aliases[] = $join['alias'];
                        $format = isset($join['condition']) ? $join['condition'] // otherwise guess condition
                            : '%1$s = %' . (array_search($join['alias'], $aliases) + 1) . '$s.%1$s';

                        if (false !== ($format = @vsprintf($format, $aliases))) {
                            $join['condition'] = $format;
                        } else {
                            $join['condition'] = null;
                        }

                        if (!isset($join['indexBy'])) {
                            $join['indexBy'] = null;
                        }

                        /**
                         * @see \Doctrine\ORM\QueryBuilder::join
                         * @see \Doctrine\ORM\QueryBuilder::innerJoin
                         * @see \Doctrine\ORM\QueryBuilder::leftJoin
                         */
                        call_user_func(
                            [$queryBuilder, $type],
                            $join[$type], $join['alias'], $join['conditionType'], $join['condition'], $join['indexBy']
                        );
                    }
                    break;
                case Select::COMBINE:
                case 'set':
                    throw new \InvalidArgumentException('UNION is not supported in DQL');
                case Select::WHERE:
                case Select::HAVING:
                    // e.g. $expr = $this->repository->expression; // \Doctrine\ORM\Query\Expr
                    //      $or = $expr->orx(
                    //          $expr->eq('User.Id', 1),
                    //          $expr->like('Role.Name', "'%user%'")
                    //      );
                    //      ['where' => $or]
                    // e.g. ['where' => \Zend\Db\Sql\Predicate\Predicate]
                    //      ['where' => "%1\$s.Id = 1 AND (%2\$s.Name = 'demo' OR %3\$s.Email LIKE 'demo%%')"]
                    //      ['where' => "Id = 1 AND Name = 'demo'"]
                    //      ['where' => ['Id' => 1, '%2$s.Name' => 'demo']]
                    //      ['where' => ['Id' => [1], '%2$s.Name' => 'demo']] // if joins exist
                    if ($v instanceof PredicateInterface) {
                        $this->transformPredicate($v, $queryBuilder, $aliases);
                    } else if (is_array($v)) {
                        $count = 0;

                        foreach ($v as $key => $value) {
                            if (!is_string($key) || ctype_space($key)) {
                                continue;
                            }

                            if (false === strpos($key, '.')) {
                                $key = $aliases[0] . '.' . trim($key);
                            } else if (false !== ($format = @vsprintf($key, $aliases))) {
                                $key = trim($format);
                            }

                            if (($isArr = is_array($value)) && isset($value['array']) && isset($value['column_key'])) {
                                $tmp = [];

                                foreach ($value['array'] as $val) {
                                    $tmp[] = $val->{$value['column_key']};
                                }

                                $value = $tmp;
                            }

                            $queryBuilder->{'and' . ucfirst($k)}(
                                $isArr
                                    ? $queryBuilder->expr()->in($key, '?' . $count)
                                    : $queryBuilder->expr()->eq($key, '?' . $count)
                            )->setParameter($count++, $value);
                        }
                    } else {
                        if (is_string($v) && false !== ($format = @vsprintf($v, $aliases))) {
                            $v = $format;
                        }

                        /**
                         * @see \Doctrine\ORM\QueryBuilder::where
                         * @see \Doctrine\ORM\QueryBuilder::having
                         */
                        $queryBuilder->$k($v);
                    }
                    break;
                case Select::GROUP:
                case 'groupBy':
                    // e.g. ['group' => '%1$s.Id, %2$s.Name']
                    //      ['group' => 'Id, Name']
                    //      ['group' => ['Id', 'Name']]
                    if (is_string($v)) {
                        $v = preg_split(CHAOS_REPLACE_COMMA_SEPARATOR, $v, -1, PREG_SPLIT_NO_EMPTY);
                    } else if (!is_array($v)) {
                        throw new \InvalidArgumentException(__METHOD__ . " expects '$k' in array format");
                    }

                    foreach ($v as $group) {
                        if (isBlank($group)) {
                            continue;
                        }

                        if (false === strpos($group, '.')) {
                            $group = $aliases[0] . '.' . $group;
                        } else if (false !== ($format = @vsprintf($group, $aliases))) {
                            $group = $format;
                        }

                        $queryBuilder->addGroupBy($group);
                    }
                    break;
                case Select::ORDER:
                case 'orderBy':
                    // e.g. ['order' => '%1$s.Id DESC, %2$s.Name']
                    //      ['order' => 'Id DESC, Name'] // equivalent to 'Id DESC, Name ASC'
                    //      ['order' => 'Id DESC NULLS FIRST, Name ASC NULLS LAST']
                    //      ['order' => ['Id DESC NULLS FIRST', 'Name ASC NULLS LAST']]
                    //      ['order' => ['Id' => 'DESC NULLS FIRST', 'Name' => 'ASC NULLS LAST']]
                    if (is_string($v)) {
                        $v = preg_split(CHAOS_REPLACE_COMMA_SEPARATOR, $v, -1, PREG_SPLIT_NO_EMPTY);
                    } else if (!is_array($v)) {
                        throw new \InvalidArgumentException(__METHOD__ . " expects '$k' in array format");
                    }

                    foreach ($v as $key => $value) {
                        if (is_string($key)) {
                            if (ctype_space($key)) {
                                continue;
                            }

                            preg_match(CHAOS_MATCH_ASC_DESC, $key . ' ' . $value, $matches);
                        } else {
                            if (isBlank($value)) {
                                continue;
                            }

                            preg_match(CHAOS_MATCH_ASC_DESC, $value, $matches);
                        }

                        if (!empty($matches[1])) {
                            if (false === strpos($matches[1], '.')) {
                                if (!isset($this->_class->fieldMappings[$matches[1]])) {
                                    continue;
                                }

                                $matches[1] = $aliases[0] . '.' . $matches[1];
                            } else if (false !== ($format = @vsprintf($matches[1], $aliases))) {
                                $parts = explode('.', $format);

                                try {
                                    /** @var \Symfony\Component\DependencyInjection\ContainerBuilder $container */
                                    $container = $this->getContainer();

                                    if (!$container->has($parts[0])
                                        || !property_exists($container->get($parts[0]), $parts[1])
                                    ) {
                                        continue;
                                    }

                                    $matches[1] = $format;
                                } catch (\Exception $ex) {
                                    throw ORMException::unknownEntityNamespace($parts[0]);
                                }
                            }

                            $order = Select::ORDER_ASCENDING;

                            if (isset($matches[2]) && Select::ORDER_DESCENDING === strtoupper($matches[2])) {
                                $order = Select::ORDER_DESCENDING;
                            }

                            if (!isBlank($matches[3])) { // NULLS FIRST, NULLS LAST
                                $order .= ' ' . trim($matches[3]);
                            }

                            $queryBuilder->addOrderBy($matches[1], $order);
                        }
                    }
                    break;
                case Select::LIMIT:
                    $queryBuilder->setMaxResults($v);
                    break;
                case Select::OFFSET:
                    $queryBuilder->setFirstResult($v);
                    break;
                case 'cacheable':
                    $queryBuilder->setCacheable($v);
                    break;
                default:
            }
        }

        return $queryBuilder;
    }

    /**
     * Converts a <tt>Predicate</tt> to a <tt>QueryBuilder</tt>.
     *
     * @param   \Zend\Db\Sql\Predicate\PredicateSet|\Zend\Db\Sql\Predicate\PredicateInterface $predicateSet
     *          The <tt>PredicateSet</tt> instance.
     * @param   \Doctrine\ORM\QueryBuilder $queryBuilder The <tt>QueryBuilder</tt> instance.
     * @param   array $aliases The aliases.
     * @return  \Doctrine\ORM\QueryBuilder
     */
    private function transformPredicate(PredicateInterface $predicateSet, QueryBuilder $queryBuilder, $aliases)
    {
        foreach ($predicateSet->getPredicates() as $value) {
            $predicate = $value[1];

            if (method_exists($predicate, 'getIdentifier')) {
                /** @var \Zend\Db\Sql\Predicate\Between|\Zend\Db\Sql\Predicate\Operator $predicate */
                if (false === strpos($identifier = $predicate->getIdentifier(), '.')) {
                    $predicate->setIdentifier($aliases[0] . '.' . $identifier);
                } else if (false !== ($format = @vsprintf($identifier, $aliases))) {
                    $predicate->setIdentifier($format);
                }
            }

            try {
                $type = reflect($predicate)->getShortName();
            } catch (\ReflectionException $ex) {
                $type = null;
            }

            switch ($type) {
                case 'Predicate': // e.g. nest/unnest
                    $expr = $this
                        ->transformPredicate($predicate, $this->createQueryBuilder($aliases[0]), $aliases)
                        ->getDQLPart('where');
                    break;
                case 'Between':
                case 'NotBetween':
                    $expr = sprintf(
                        $predicate->getSpecification(), $predicate->getIdentifier(),
                        $predicate->getMinValue(), $predicate->getMaxValue()
                    );
                    break;
                case 'Expression':
                    /** @var \Zend\Db\Sql\Predicate\Expression $predicate */
                    $expr = $predicate->getExpression();
                    $queryBuilder->setParameters($predicate->getParameters());
                    break;
                case 'In':
                case 'NotIn':
                    /**
                     * @see \Doctrine\ORM\Query\Expr::in
                     * @see \Doctrine\ORM\Query\Expr::notIn
                     * @var \Zend\Db\Sql\Predicate\In $predicate
                     */
                    $expr = $queryBuilder->expr()
                        ->{lcfirst($type)}($predicate->getIdentifier(), $predicate->getValueSet());
                    break;
                case 'IsNotNull':
                case 'IsNull':
                    /**
                     * @see \Doctrine\ORM\Query\Expr::isNull
                     * @see \Doctrine\ORM\Query\Expr::isNotNull
                     * @var \Zend\Db\Sql\Predicate\IsNull $predicate
                     */
                    $expr = $queryBuilder->expr()
                        ->{lcfirst($type)}($predicate->getIdentifier());
                    break;
                case 'Like':
                case 'NotLike':
                    /**
                     * @see \Doctrine\ORM\Query\Expr::like
                     * @see \Doctrine\ORM\Query\Expr::notLike
                     * @var \Zend\Db\Sql\Predicate\Like $predicate
                     */
                    $expr = $queryBuilder->expr()
                        ->{lcfirst($type)}($predicate->getIdentifier(), $predicate->getLike());
                    break;
                case 'Literal':
                    /** @var \Zend\Db\Sql\Predicate\Literal $predicate */
                    $expr = $queryBuilder->expr()
                        ->literal($predicate->getLiteral());
                    $expr = trim($expr->getParts()[0], "'");

                    if (false !== ($format = @vsprintf($expr, $aliases))) {
                        $expr = $format;
                    }
                    break;
                default:
                    if (PredicateInterface::TYPE_IDENTIFIER === $predicate->getLeftType()) {
                        $left = $predicate->getLeft();
                        $right = $predicate->getRight();
                    } else {
                        $left = $predicate->getRight();
                        $right = $predicate->getLeft();
                    }

                    if (false === strpos($left, '.')) {
                        $left = $aliases[0] . '.' . $left;
                    } else if (false !== ($format = @vsprintf($left, $aliases))) {
                        $left = $format;
                    }

                    $expr = new Comparison($left, $predicate->getOperator(), $right);
            }

            /**
             * @see \Doctrine\ORM\QueryBuilder::andWhere
             * @see \Doctrine\ORM\QueryBuilder::orWhere
             * @see \Doctrine\ORM\QueryBuilder::andHaving
             * @see \Doctrine\ORM\QueryBuilder::orHaving
             */
            $queryBuilder->{strtolower($value[0]) . 'Where'}($expr);
        }

        return $queryBuilder;
    }
}
