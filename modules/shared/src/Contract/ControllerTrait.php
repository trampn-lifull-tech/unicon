<?php

namespace Chaos\Common\Contract;

use Carbon\Carbon;
use Chaos\Common\Constant\PredicateType;
use Chaos\Common\Type\Type;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Filter\StaticFilter;

/**
 * Trait ControllerTrait
 * @author ntd1712
 */
trait ControllerTrait
{
    /**
     * Gets the filter parameters.
     *
     * ?filter=[
     *  {"predicate":"equalTo","left":"Id","right":"1","leftType":"identifier","rightType":"value",
     *      "combine":"AND","nesting":"nest"},
     *  {"predicate":"equalTo","left":"Id","right":"2","leftType":"identifier","rightType":"value","combine":"OR"},
     *  {"predicate":"like","identifier":"Name","like":"demo","combine":"and","nesting":"unnest"}
     * ]
     *
     * // equals to `(Id = 1 or Id = 2) and Name like 'demo'`, and is equivalent to:
     * $predicate = new \Zend\Db\Sql\Predicate\Predicate;
     * $predicate->nest()
     *  ->equalTo('Id', 1)
     *  ->or
     *  ->equalTo('Id', 2)
     *  ->unnest()
     *  ->and
     *  ->like('Name', 'demo');
     *
     * Parameters allowed:
     *
     * ?filter=[
     *  {"predicate":"between|notBetween","identifier":"CreatedAt","minValue":"9/29/2014","maxValue":"10/29/2014",
     *      "combine":"AND|OR","nesting":"nest|unnest"},
     *  {"predicate":"equalTo|notEqualTo|lessThan|greaterThan|lessThanOrEqualTo|greaterThanOrEqualTo",
     *      "left":"Name","right":"ntd1712","leftType":"identifier","rightType":"value",
     *      "combine":"AND|OR","nesting":"nest|unnest"},
     *  {"predicate":"expression","expression":"CONCAT(?0,?1) IS NOT NULL","parameters":["CreatedAt","UpdatedAt"],
     *      "combine":"AND|OR","nesting":"nest|unnest"}
     *  {"predicate":"in|notIn","identifier":"Name","valueSet":["ntd1712","moon",17],
     *      "combine":"AND|OR","nesting":"nest|unnest"},
     *  {"predicate":"isNull|isNotNull","identifier":"Name","combine":"AND|OR","nesting":"nest|unnest"},
     *  {"predicate":"like|notLike","identifier":"Name","like|notLike":"ntd1712",
     *      "combine":"AND|OR","nesting":"nest|unnest"}
     *  {"predicate":"literal","literal":"NotUse=false","combine":"AND|OR","nesting":"nest|unnest"}
     * ]
     * &filter=ntd1712
     *
     * Usage allowed:
     *
     * $binds = [
     *  'where' => 'Id = 1 OR Name = "ntd1712"',
     *  'where' => ['Id' => 1, 'Name' => 'ntd1712'] // aka. 'Id = 1 AND Name = "ntd1712"'
     *  'where' => ['Id' => 1, 'Name = "ntd1712"']  // aka. 'Id = 1 AND Name = "ntd1712"'
     *  'where' => $predicate
     * ]
     *
     * @param   array $request The request.
     * @param   array $permit [optional] This is useful for limiting which scalars should be allowed.
     * @param   array $binds [optional] A bind variable array.
     * @return  array
     * @throws  \ReflectionException
     */
    protected function getFilterParams(array $request, array $permit = [], array $binds = [])
    {
        $filter = $request['filter'] ?? null;

        if (!isBlank($filter)) {
            if (is_string($filter)) {
                $filter = trim(rawurldecode($filter));

                if (false !== ($decodedValue = isJson($filter, true))) {
                    $filter = $decodedValue;
                }
            }

            $filterSet = $this->filterParams($filter, $permit);

            if (0 !== count($filterSet)) {
                if (isset($binds['where'])) {
                    $filterSet->addPredicates($binds['where']);
                }

                $binds['where'] = $filterSet;
            }
        }

        return $this->getOrderParams($request, $binds);
    }

    /**
     * Gets the sort order parameters.
     *
     * Parameters allowed:
     *
     * ?sort=[
     *  {"property":"Id","direction":"desc","nulls":"first"},
     *  {"property":"Name","direction":"asc","nulls":"last"}
     * ]
     * &sort=name&direction=desc&nulls=first
     *
     * Usage allowed:
     *
     * $binds = [
     *  'order' => 'Id DESC, Name',
     *  'order' => 'Id DESC NULLS FIRST, Name ASC NULLS LAST',
     *  'order' => ['Id DESC NULLS FIRST', 'Name ASC NULLS LAST'],
     *  'order' => ['Id' => 'DESC NULLS FIRST', 'Name' => 'ASC NULLS LAST']
     * ]
     *
     * @param   array $request The request.
     * @param   array $binds [optional] A bind variable array.
     * @return  array
     * @throws  \ReflectionException
     */
    protected function getOrderParams(array $request, array $binds = [])
    {
        $order = $request['sort'] ?? null;

        if (!isBlank($order)) {
            if (is_string($order)) {
                $order = trim(rawurldecode($order));

                if (false !== ($decodedValue = isJson($order, true))) {
                    $order = (array)$decodedValue;
                } else {
                    $order = [
                        [
                            'property' => $order,
                            'direction' => $request['direction'] ?? null,
                            'nulls' => $request['nulls'] ?? null
                        ]
                    ];
                }
            }

            $orderSet = $this->filterOrderParams($order);

            if (!empty($orderSet)) {
                if (empty($binds['order'])) {
                    $binds['order'] = [];

                    foreach ($orderSet as $k => $v) {
                        $binds['order'][$k] = $v;
                    }
                } else {
                    $isArray = is_array($binds['order']);
                    $isString = is_string($binds['order']);

                    foreach ($orderSet as $k => $v) {
                        if ($isArray) {
                            $binds['order'][$k] = $v;
                        } else if ($isString) {
                            $binds['order'] .= ', ' . $k . ' ' . $v;
                        }
                    }
                }
            }
        }

        return $binds;
    }

    /**
     * Gets the pager parameters.
     *
     * Parameters allowed:
     *
     *  ?page=1&length=10
     *  ?start=0&length=10
     *
     * Usage allowed:
     *
     * $binds = [
     *  'CurrentPageStart' => 0,
     *  'CurrentPageNumber' => 1,
     *  'ItemCountPerPage' => 10
     * ]
     *
     * @param   array $request The request.
     * @param   array $binds [optional] A bind variable array.
     * @return  bool|array
     */
    protected function getPagerParams(array $request, array $binds = [])
    {
        $default = [
            'CurrentPageStart' => $request['start'] ?? null,
            'CurrentPageNumber' => $request['page'] ?? null,
            'ItemCountPerPage' => $request['length'] ?? null,
        ];

        return $this->filterPagerParams(empty($binds) ? $default : $binds + $default);
    }

    // <editor-fold desc="Filter methods" defaultstate="collapsed">

    /**
     * Returns the string $value, converting characters to their corresponding HTML entity equivalents where they exist.
     *
     * @param   string $value The value.
     * @param   bool $checkDate [optional].
     * @return  string
     */
    protected function filter($value, $checkDate = false)
    {
        if (isBlank($value) || !is_scalar($value)) {
            return '';
        }

        /** @var \M1\Vars\Vars $vars */
        $vars = $this->getVars();
        $value = trim($value);

        if (false !== $checkDate && false !== ($time = strtotime($value))) {
            $carbon = Carbon::createFromTimestamp($time, $vars->get('app.timezone'));

            if (is_int($checkDate)) {
                $carbon->addSeconds($checkDate);
            }

            $filtered = $carbon->toDateTimeString();
        } else {
            $filtered = StaticFilter::execute(
                $value,
                'HtmlEntities',
                ['encoding' => $vars->get('app.charset')]
            );
        }

        return $filtered;
    }

    /**
     * Filters parameters.
     *
     * @param   array|string $binds A bind variable array.
     * @param   array $permit [optional] This is useful for limiting which scalars should be allowed.
     * @param   null|\Zend\Db\Sql\Predicate\PredicateInterface $predicate [optional] The <tt>Predicate</tt> instance.
     * @return  Predicate|\Zend\Db\Sql\Predicate\PredicateInterface
     */
    protected function filterParams($binds = [], $permit = [], $predicate = null)
    {
        if (null === $predicate) {
            $predicate = new Predicate;
        }

        /** @var \M1\Vars\Vars $vars */
        $vars = $this->getVars();

        if (is_array($binds)) {
            foreach ($binds as $v) {
                if (!is_array($v) || empty($v['predicate'])) {
                    continue;
                }

                if (isset($v['nesting'])
                    && (PredicateType::NEST === $v['nesting'] || PredicateType::UNNEST === $v['nesting'])
                ) {
                    /**
                     * @see Predicate::nest
                     * @see Predicate::unnest
                     */
                    $predicate = $predicate->{$v['nesting']}();
                }

                if (isset($v['combine'])
                    && (Predicate::OP_OR === $v['combine'] || strtolower(Predicate::OP_OR) === $v['combine'])
                ) {
                    $predicate->or;
                }

                switch ($v['predicate']) {
                    case PredicateType::BETWEEN:
                    case PredicateType::NOT_BETWEEN:
                        if (empty($v['identifier']) || !isset($v['minValue']) || !isset($v['maxValue'])
                            || !isset($permit[$v['identifier']])
                        ) {
                            continue;
                        }

                        /**
                         * @see Predicate::between
                         * @see Predicate::notBetween
                         */
                        $predicate->{$v['predicate']}(
                            $v['identifier'],
                            "'" . $this->filter($v['minValue'], true) . "'",
                            "'" . $this->filter($v['maxValue'], 86399) . "'"
                        );
                        break;
                    case PredicateType::EQUAL_TO:
                    case PredicateType::NOT_EQUAL_TO:
                    case PredicateType::GREATER_THAN:
                    case PredicateType::LESS_THAN:
                    case PredicateType::GREATER_THAN_OR_EQUAL_TO:
                    case PredicateType::LESS_THAN_OR_EQUAL_TO:
                    case PredicateType::EQ:
                    case PredicateType::NEQ:
                    case PredicateType::GT:
                    case PredicateType::LT:
                    case PredicateType::GTE:
                    case PredicateType::LTE:
                        if (!isset($v['left']) || !isset($v['right'])) {
                            continue;
                        }

                        if (empty($v['leftType']) || Predicate::TYPE_VALUE !== $v['leftType']) {
                            $v['leftType'] = Predicate::TYPE_IDENTIFIER;
                        }

                        if (empty($v['rightType']) || Predicate::TYPE_IDENTIFIER !== $v['rightType']) {
                            $v['rightType'] = Predicate::TYPE_VALUE;
                        }

                        if ($v['leftType'] == $v['rightType']) {
                            $v['leftType'] = Predicate::TYPE_IDENTIFIER;
                            $v['rightType'] = Predicate::TYPE_VALUE;
                        }

                        if (Predicate::TYPE_IDENTIFIER !== $v['leftType']) {
                            $v['left'] = "'" . $this->filter($v['left'], true) . "'";
                        } else if (!isset($permit[$v['left']])) {
                            continue;
                        }

                        if (Predicate::TYPE_IDENTIFIER !== $v['rightType']) {
                            $v['right'] = "'" . $this->filter($v['right'], true) . "'";
                        } else if (!isset($permit[$v['right']])) {
                            continue;
                        }

                        /**
                         * @see Predicate::equalTo
                         * @see Predicate::notEqualTo
                         * @see Predicate::lessThan
                         * @see Predicate::greaterThan
                         * @see Predicate::lessThanOrEqualTo
                         * @see Predicate::greaterThanOrEqualTo
                         */
                        $predicate->{$v['predicate']}($v['left'], $v['right'], $v['leftType'], $v['rightType']);
                        break;
                    case PredicateType::EXPR:
                    case PredicateType::EXPRESSION:
                        if (empty($v['expression'])) {
                            continue;
                        }

                        if (isset($v['parameters'])) {
                            if (!is_array($v['parameters'])) {
                                $v['parameters'] = [$v['parameters']];
                            }

                            foreach ($v['parameters'] as $key => &$value) {
                                if (!is_scalar($value)) {
                                    unset($v['parameters'][$key]);
                                } else if (!isset($permit[$value])) {
                                    $value = "'" . str_replace('%', '%%', $this->filter($value)) . "'";
                                }
                            }

                            unset($value);
                            $v['parameters'] = array_values($v['parameters']);
                        }

                        $v['expression'] = str_replace(['&lt;', '&gt;'], ['<', '>'], $this->filter($v['expression']));
                        $predicate->expression($v['expression'], @$v['parameters']);
                        break;
                    case PredicateType::IN:
                    case PredicateType::NIN:
                    case PredicateType::NOT_IN:
                        if (empty($v['identifier']) || empty($v['valueSet']) || !is_array($v['valueSet'])) {
                            continue;
                        }

                        if (is_array($v['identifier'])) {
                            foreach ($v['identifier'] as $key => $value) {
                                if (!isset($permit[$value])) {
                                    unset($v['identifier'][$key]);
                                }
                            }

                            if (empty($v['identifier'])) {
                                continue;
                            }

                            $v['identifier'] = array_values($v['identifier']);
                        } else if (!isset($permit[$v['identifier']])) {
                            continue;
                        }

                        foreach ($v['valueSet'] as &$value) {
                            $value = "'" . $this->filter($value) . "'";
                        }
                        unset($value);

                        /**
                         * @see Predicate::in
                         * @see Predicate::notIn
                         */
                        $predicate->{$v['predicate']}($v['identifier'], $v['valueSet']);
                        break;
                    case PredicateType::IS_NOT_NULL:
                    case PredicateType::IS_NULL:
                        if (empty($v['identifier']) || !isset($permit[$v['identifier']])) {
                            continue;
                        }

                        /**
                         * @see Predicate::isNull
                         * @see Predicate::isNotNull
                         */
                        $predicate->{$v['predicate']}($v['identifier']);
                        break;
                    case PredicateType::LIKE:
                    case PredicateType::NOT_LIKE:
                        if (empty($v['identifier']) || empty($v[$v['predicate']])
                            || !isset($permit[$v['identifier']]) || !is_string($v[$v['predicate']])
                        ) {
                            continue;
                        }

                        $value = str_replace('%', '%%', $this->filter($v[$v['predicate']]));
                        $v[$v['predicate']] = "'%$value%'";

                        /**
                         * @see Predicate::like
                         * @see Predicate::notLike
                         */
                        $predicate->{$v['predicate']}($v['identifier'], $v[$v['predicate']]);
                        break;
                    case Predicate::TYPE_LITERAL:
                        if (empty($v['literal']) || !is_string($v['literal'])) {
                            continue;
                        }

                        $predicate->literal(
                            str_replace(
                                ['&lt;', '&gt;', '&#39;', '&#039;'], ['<', '>', "'", "'"], $this->filter($v['literal'])
                            )
                        );
                        break;
                    default:
                }
            }
        } else if (is_string($binds)) {
            $predicateSet = new Predicate;
            $searchable = $vars->get('app.min_search_chars') <= strlen($binds);
            $binds = $this->filter($binds);

            $equalValue = "'" . $binds . "'";
            $likeValue = "'%" . str_replace('%', '%%', $binds) . "%'";
            $count = 0;

            foreach ($permit as $k => $v) {
                if ((Type::STRING_TYPE === $v['type'] || Type::TEXT_TYPE === $v['type'])
                    && ($searchable || ($isChar = isset($v['options']) && isset($v['options']['fixed'])))
                ) {
                    $predicateSet->or;
                    isset($isChar) && $isChar
                        ? $predicateSet->equalTo($k, $equalValue)
                        : $predicateSet->like($k, $likeValue);

                    if (CHAOS_QUERY_LIMIT <= ++$count) {
                        break;
                    }
                }
            }

            if (0 !== count($predicateSet)) {
                $predicate->predicate($predicateSet);
            }
        }

        return $predicate;
    }

    /**
     * Filters the order parameters.
     *
     * @param   array $binds A bind variable array.
     * @return  array
     * @throws  \ReflectionException
     */
    protected function filterOrderParams(array $binds = [])
    {
        $orderSet = [];
        $count = 0;

        foreach ($binds as $v) {
            if (!is_array($v) || empty($v['property'])) {
                continue;
            }

            $orderSet[$v['property']] = empty($v['direction']) || !is_string($v['direction'])
            || PredicateType::DESC !== strtoupper($v['direction'])
                ? PredicateType::ASC : PredicateType::DESC;

            if (!empty($v['nulls']) && PredicateType::has($nulls = 'NULLS ' . strtoupper($v['nulls']))) {
                $orderSet[$v['property']] .= ' ' . (
                    PredicateType::NULLS_FIRST === $nulls ? PredicateType::NULLS_FIRST : PredicateType::NULLS_LAST
                );
            }

            if (CHAOS_QUERY_LIMIT <= ++$count) {
                break;
            }
        }

        return $orderSet;
    }

    /**
     * Filters the pager parameters.
     *
     * @param   array $binds A bind variable array.
     * @return  bool|array
     */
    protected function filterPagerParams(array $binds = [])
    {
        if (!(($hasCurrentPageNumber = isset($binds['CurrentPageNumber'])) || isset($binds['CurrentPageStart']))) {
            return false;
        }

        if (isset($binds['ItemCountPerPage'])) {
            $binds['ItemCountPerPage'] = (int)$binds['ItemCountPerPage'];

            if (1 > $binds['ItemCountPerPage']) {
                $binds['ItemCountPerPage'] = 1;
            } else if (($maxPerPage = CHAOS_MAX_ROWS_PER_QUERY) < $binds['ItemCountPerPage']) {
                $binds['ItemCountPerPage'] = $maxPerPage;
            }
        } else {
            $binds['ItemCountPerPage'] = CHAOS_QUERY_LIMIT;
        }

        if ($hasCurrentPageNumber) {
            $binds['CurrentPageNumber'] = (int)$binds['CurrentPageNumber'];

            if (1 > $binds['CurrentPageNumber']) {
                $binds['CurrentPageNumber'] = 1;
            }

            if (!isset($binds['CurrentPageStart'])) {
                $binds['CurrentPageStart'] = $binds['ItemCountPerPage'] * ($binds['CurrentPageNumber'] - 1);

                return $binds;
            }
        }

        if (isset($binds['CurrentPageStart'])) {
            $binds['CurrentPageStart'] = (int)$binds['CurrentPageStart'];

            if (0 > $binds['CurrentPageStart']) {
                $binds['CurrentPageStart'] = 0;
            }

            if (!$hasCurrentPageNumber) {
                $binds['CurrentPageNumber'] = $binds['CurrentPageStart'] / $binds['ItemCountPerPage'] + 1;
            }
        }

        return $binds;
    }

    // </editor-fold>
}
