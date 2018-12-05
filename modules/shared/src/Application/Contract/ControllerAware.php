<?php

namespace Chaos\Common\Application\Contract;

use Chaos\Common\Support\Enum;

/**
 * @todo
 *
 * Trait ControllerAware
 * @author ntd1712
 */
trait ControllerAware
{
    /**
     * Gets filter parameters.
     * e.g.
     * ?filter=[
     *  {"predicate":"equalTo","left":"Id","right":"1","leftType":"identifier","rightType":"value",
     *      "combine":"AND","nesting":"nest"},
     *  {"predicate":"equalTo","left":"Id","right":"2","leftType":"identifier","rightType":"value","combine":"OR"},
     *  {"predicate":"like","identifier":"Name","like":"demo","combine":"and","nesting":"unnest"}
     * ]
     * // equivalent to
     * $predicate = new \Zend\Db\Sql\Predicate\Predicate;
     * $predicate->nest()
     *  ->equalTo('Id', 1)
     *  ->or
     *  ->equalTo('Id', 2)
     *  ->unnest()
     *  ->and
     *  ->like('Name', '%demo%');
     *
     * Allows parameters:
     * ?filter=[
     *  {"predicate":"between|notBetween","identifier":"EditedAt","minValue":"9/29/2014","maxValue":"10/29/2014",
     *      "combine":"AND|OR","nesting":"nest|unnest"},
     *  {"predicate":"equalTo|notEqualTo|lessThan|greaterThan|lessThanOrEqualTo|greaterThanOrEqualTo",
     *   "left":"Name","right":"ntd1712","leftType":"identifier","rightType":"value",
     *      "combine":"AND|OR","nesting":"nest|unnest"},
     *  {"predicate":"expression","expression":"CONCAT(?0,?1) IS NOT NULL","parameters":["AddedAt","EditedAt"],
     *      "combine":"AND|OR","nesting":"nest|unnest"}
     *  {"predicate":"in|notIn","identifier":"Name","valueSet":["ntd1712","dzung",3],
     *      "combine":"AND|OR","nesting":"nest|unnest"},
     *  {"predicate":"isNull|isNotNull","identifier":"Name","combine":"AND|OR","nesting":"nest|unnest"},
     *  {"predicate":"like|notLike","identifier":"Name","like|notLike":"ntd1712",
     *      "combine":"AND|OR","nesting":"nest|unnest"}
     *  {"predicate":"literal","literal":"IsDeleted=false","combine":"AND|OR","nesting":"nest|unnest"}
     * ]
     * &filter=ntd1712
     *
     * Allows declarations: $binds = [
     *  'where' => 'Id = 1 OR Name = "ntd1712"',
     *  'where' => ['Id' => 1, 'Name' => 'ntd1712'] // aka. 'Id = 1 AND Name = "ntd1712"'
     *  'where' => ['Id' => 1, 'Name = "ntd1712"']  // aka. 'Id = 1 AND Name = "ntd1712"'
     *  'where' => new \Zend\Db\Sql\Predicate\Predicate
     * ]
     *
     * @param   array $binds A bind variable array.
     * @param   string $key The request parameter key; defaults to <b>filter</b>.
     * @return  array
     * @throws  \ReflectionException
     */
    protected function getFilterParams(array $binds = [], $key = 'filter')
    {
        $filter = $this->getRequest($key);

        if (!isBlank($filter)) {
            if (is_string($filter)) {
                $filter = trim(rawurldecode($filter));

                if (false !== ($decodedValue = isJson($filter, true))) {
                    $filter = $decodedValue;
                }
            }

            /** @var \Zend\Db\Sql\Predicate\PredicateSet $filterSet */
            $filterSet = $this->getService()->prepareFilterParams($filter);

            if (0 !== count($filterSet)) {
                if (isset($binds['where'])) {
                    $filterSet->addPredicates($binds['where']);
                }

                $binds['where'] = $filterSet;
            }
        }

        return $this->getOrderParams($binds);
    }

    /**
     * Gets sort order parameters.
     *
     * Allows parameters:
     * ?sort=[
     *  {"property":"Id","direction":"desc","nulls":"first"},
     *  {"property":"Name","direction":"asc","nulls":"last"}
     * ]
     * &sort=name&direction=desc&nulls=first
     *
     * Allows declarations: $binds = [
     *  'order' => 'Id DESC, Name',
     *  'order' => 'Id DESC NULLS FIRST, Name ASC NULLS LAST',
     *  'order' => ['Id DESC NULLS FIRST', 'Name ASC NULLS LAST'],
     *  'order' => ['Id' => 'DESC NULLS FIRST', 'Name' => 'ASC NULLS LAST']
     * ]
     *
     * @param   array $binds A bind variable array.
     * @param   string $key The request parameter key; defaults to <b>sort</b>.
     * @return  array
     * @throws  \ReflectionException
     */
    protected function getOrderParams(array $binds = [], $key = 'sort')
    {
        $order = $this->getRequest($key);

        if (!isBlank($order)) {
            if (is_string($order)) {
                $order = trim(rawurldecode($order));

                if (false !== ($decodedValue = isJson($order, true))) {
                    $order = (array) $decodedValue;
                } else {
                    $order = [[
                        'property' => $order,
                        'direction' => $this->getRequest('direction'),
                        'nulls' => $this->getRequest('nulls')
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
     * Allows parameters:
     *  ?page=1&length=10
     *  ?start=0&length=10
     *
     * Allows declarations: $binds = [
     *  'CurrentPageStart' => 0,
     *  'CurrentPageNumber' => 1,
     *  'ItemCountPerPage' => 10
     * ]
     *
     * @param   array $binds A bind variable array.
     * @param   array $keys The request parameter keys; defaults to <b>['page', 'length']</b>.
     * @return  boolean|array
     */
    protected function getPagerParams(array $binds = [], array $keys = ['page', 'length'])
    {
        $default = [
            'CurrentPageStart' => $this->getRequest('start'),
            'CurrentPageNumber' => $this->getRequest(@$keys[0]),
            'ItemCountPerPage' => $this->getRequest(@$keys[1])
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
            || Enum\PredicateType::DESC !== strtoupper($v['direction'])
                ? Enum\PredicateType::ASC : Enum\PredicateType::DESC;

            if (!empty($v['nulls']) && Enum\PredicateType::has($nulls = 'NULLS ' . strtoupper($v['nulls']))) {
                $orderSet[$v['property']] .= ' ' . (Enum\PredicateType::NULLS_FIRST === $nulls
                    ? Enum\PredicateType::NULLS_FIRST : Enum\PredicateType::NULLS_LAST);
            }

            if (CHAOS_MAX_QUERY <= ++$count) {
                break;
            }
        }

        return $orderSet;
    }

    /**
     * Prepares pager parameters.
     *
     * @param   array $binds A bind variable array.
     * @return  boolean|array
     */
    private function preparePagerParams(array $binds = [])
    {
        if (!(($hasCurrentPageNumber = isset($binds['CurrentPageNumber'])) || isset($binds['CurrentPageStart']))) {
            return false;
        }

        if (isset($binds['ItemCountPerPage'])) {
            $binds['ItemCountPerPage'] = (int) $binds['ItemCountPerPage'];

            if (1 > $binds['ItemCountPerPage']) {
                $binds['ItemCountPerPage'] = 1;
            } else if (($maxPerPage = $this->getVars()->get('app.max_items_per_page')) < $binds['ItemCountPerPage']) {
                $binds['ItemCountPerPage'] = $maxPerPage;
            }
        } else {
            $binds['ItemCountPerPage'] = $this->getVars()->get('app.items_per_page');
        }

        if ($hasCurrentPageNumber) {
            $binds['CurrentPageNumber'] = (int) $binds['CurrentPageNumber'];

            if (1 > $binds['CurrentPageNumber']) {
                $binds['CurrentPageNumber'] = 1;
            }

            if (!isset($binds['CurrentPageStart'])) {
                $binds['CurrentPageStart'] = $binds['ItemCountPerPage'] * ($binds['CurrentPageNumber'] - 1);

                return $binds;
            }
        }

        if (isset($binds['CurrentPageStart'])) {
            $binds['CurrentPageStart'] = (int) $binds['CurrentPageStart'];

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
