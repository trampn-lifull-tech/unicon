<?php

namespace Chaos\Common\Contract;

use Chaos\Common\Constant\PredicateType;

/**
 * Trait ControllerTrait
 * @author ntd1712
 */
trait ControllerTrait
{
    /**
     * @var \Chaos\Common\Service\Service|\Chaos\Common\Service\Contract\IService
     */
    protected $service;

    /**
     * Gets filter parameters.
     *
     * ?filter=[
     *  {"predicate":"equalTo","left":"Id","right":"1","leftType":"identifier","rightType":"value",
     *      "combine":"AND","nesting":"nest"},
     *  {"predicate":"equalTo","left":"Id","right":"2","leftType":"identifier","rightType":"value","combine":"OR"},
     *  {"predicate":"like","identifier":"Name","like":"demo","combine":"and","nesting":"unnest"}
     * ]
     *
     * // ... is equivalent to
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
     * @param   array $binds [optional] A bind variable array.
     * @param   string $key [optional] The request parameter key; defaults to <b>filter</b>.
     * @return  array
     * @throws  \ReflectionException
     */
    protected function getFilterParams(array $request, array $binds = [], $key = 'filter')
    {
        $filter = $request[$key] ?? null;

        if (!isBlank($filter)) {
            if (is_string($filter)) {
                $filter = trim(rawurldecode($filter));

                if (false !== ($decodedValue = isJson($filter, true))) {
                    $filter = $decodedValue;
                }
            }

            $filterSet = $this->service->prepareFilterParams($filter);

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
     * Gets sort order parameters.
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
     * @param   array $binds A bind variable array.
     * @param   array $keys [optional] The request parameter keys; defaults to <b>['sort', 'direction', 'nulls']</b>.
     * @return  array
     * @throws  \ReflectionException
     */
    protected function getOrderParams(array $request, array $binds = [], array $keys = ['sort', 'direction', 'nulls'])
    {
        $order = $request[@$keys[0]] ?? null;

        if (!isBlank($order)) {
            if (is_string($order)) {
                $order = trim(rawurldecode($order));

                if (false !== ($decodedValue = isJson($order, true))) {
                    $order = (array)$decodedValue;
                } else {
                    $order = [[
                        'property' => $order,
                        'direction' => $request[@$keys[1]] ?? null,
                        'nulls' => $request[@$keys[2]] ?? null
                    ]];
                }
            }

            $orderSet = $this->prepareOrderParams($order);

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
     * Gets pager parameters.
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
     * @param   array $keys [optional] The request parameter keys; defaults to <b>['start', 'page', 'length']</b>.
     * @return  bool|array
     */
    protected function getPagerParams(array $request, array $binds = [], array $keys = ['start', 'page', 'length'])
    {
        $default = [
            'CurrentPageStart' => $request[@$keys[0]] ?? null,
            'CurrentPageNumber' => $request[@$keys[1]] ?? null,
            'ItemCountPerPage' => $request[@$keys[2]] ?? null,
        ];

        return $this->preparePagerParams(empty($binds) ? $default : $binds + $default);
    }

    // <editor-fold desc="Private methods" defaultstate="collapsed">

    /**
     * Prepares order parameters.
     *
     * @param   array $binds A bind variable array.
     * @return  array
     * @throws  \ReflectionException
     */
    private function prepareOrderParams(array $binds = [])
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

            if (CHAOS_SQL_LIMIT <= ++$count) {
                break;
            }
        }

        return $orderSet;
    }

    /**
     * Prepares pager parameters.
     *
     * @param   array $binds A bind variable array.
     * @return  bool|array
     */
    private function preparePagerParams(array $binds = [])
    {
        if (!(($hasCurrentPageNumber = isset($binds['CurrentPageNumber'])) || isset($binds['CurrentPageStart']))) {
            return false;
        }

        if (isset($binds['ItemCountPerPage'])) {
            $binds['ItemCountPerPage'] = (int)$binds['ItemCountPerPage'];

            if (1 > $binds['ItemCountPerPage']) {
                $binds['ItemCountPerPage'] = 1;
            } else if (($maxPerPage = CHAOS_SQL_MAX_LIMIT) < $binds['ItemCountPerPage']) {
                $binds['ItemCountPerPage'] = $maxPerPage;
            }
        } else {
            $binds['ItemCountPerPage'] = CHAOS_SQL_LIMIT;
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
