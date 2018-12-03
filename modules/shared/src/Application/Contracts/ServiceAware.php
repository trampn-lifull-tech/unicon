<?php

namespace Chaos\Common\Application\Contracts;

use Zend\Db\Sql\Predicate\Predicate;
use Zend\Filter\StaticFilter;
use Chaos\Common\Support\Constants\PredicateType;

/**
 * Class ServiceAware
 * @author ntd1712
 *
 * @method \M1\Vars\Vars getVars()
 * @method self getRepository(string $name = null)
 *
 * @todo
 */
trait ServiceAware
{
    /**
     * @var boolean A value that indicates whether the transaction is enabled.
     */
    public $enableTransaction = false;

    /**
     * Prepares filter parameters.
     *
     * @param   array|string $binds A bind variable array.
     * @param   null|\Zend\Db\Sql\Predicate\PredicateInterface $predicate [optional] The <tt>Predicate</tt> instance.
     * @return  Predicate|\Zend\Db\Sql\Predicate\PredicateInterface
     */
    public function prepareFilterParams($binds = [], $predicate = null)
    {
        if (null === $predicate) {
            $predicate = new Predicate;
        }

        $fields = $this->getRepository()->fields;

        if (is_array($binds)) {
            foreach ($binds as $v) {
                if (!is_array($v) || empty($v['predicate'])) {
                    continue;
                }

                if (isset($v['nesting'])
                    && (PredicateType::NEST === $v['nesting'] || PredicateType::UNNEST === $v['nesting'])
                ) {
                    /** @see \Zend\Db\Sql\Predicate\Predicate::nest
                      * @see \Zend\Db\Sql\Predicate\Predicate::unnest */
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
                            || !isset($fields[$v['identifier']])
                        ) {
                            continue;
                        }

                        /** @see \Zend\Db\Sql\Predicate\Predicate::between
                          * @see \Zend\Db\Sql\Predicate\Predicate::notBetween */
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
                        } else if (!isset($fields[$v['left']])) {
                            continue;
                        }

                        if (Predicate::TYPE_IDENTIFIER !== $v['rightType']) {
                            $v['right'] = "'" . $this->filter($v['right'], true) . "'";
                        } else if (!isset($fields[$v['right']])) {
                            continue;
                        }

                        /** @see \Zend\Db\Sql\Predicate\Predicate::equalTo
                          * @see \Zend\Db\Sql\Predicate\Predicate::notEqualTo
                          * @see \Zend\Db\Sql\Predicate\Predicate::lessThan
                          * @see \Zend\Db\Sql\Predicate\Predicate::greaterThan
                          * @see \Zend\Db\Sql\Predicate\Predicate::lessThanOrEqualTo
                          * @see \Zend\Db\Sql\Predicate\Predicate::greaterThanOrEqualTo */
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
                                } else if (!isset($fields[$value])) {
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
                                if (!isset($fields[$value])) {
                                    unset($v['identifier'][$key]);
                                }
                            }

                            if (empty($v['identifier'])) {
                                continue;
                            }

                            $v['identifier'] = array_values($v['identifier']);
                        } else if (!isset($fields[$v['identifier']])) {
                            continue;
                        }

                        foreach ($v['valueSet'] as &$value) {
                            $value = "'" . $this->filter($value) . "'";
                        }
                        unset($value);

                        /** @see \Zend\Db\Sql\Predicate\Predicate::in
                          * @see \Zend\Db\Sql\Predicate\Predicate::notIn */
                        $predicate->{$v['predicate']}($v['identifier'], $v['valueSet']);
                        break;
                    case PredicateType::IS_NOT_NULL:
                    case PredicateType::IS_NULL:
                        if (empty($v['identifier']) || !isset($fields[$v['identifier']])) {
                            continue;
                        }

                        /** @see \Zend\Db\Sql\Predicate\Predicate::isNull
                          * @see \Zend\Db\Sql\Predicate\Predicate::isNotNull */
                        $predicate->{$v['predicate']}($v['identifier']);
                        break;
                    case PredicateType::LIKE:
                    case PredicateType::NOT_LIKE:
                        if (empty($v['identifier']) || empty($v[$v['predicate']])
                            || !isset($fields[$v['identifier']]) || !is_string($v[$v['predicate']])
                        ) {
                            continue;
                        }

                        $value = str_replace('%', '%%', $this->filter($v[$v['predicate']]));
                        $v[$v['predicate']] = "'%$value%'";

                        /** @see \Zend\Db\Sql\Predicate\Predicate::like
                          * @see \Zend\Db\Sql\Predicate\Predicate::notLike */
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
            $searchable = $this->getVars()->get('app.min_search_chars') <= strlen($binds);
            $binds = $this->filter($binds);
            $equalValue = "'" . $binds . "'";
            $likeValue = "'%" . str_replace('%', '%%', $binds) . "%'";
            $count = 0;

            foreach ($fields as $k => $v) {
                if ((Types\Type::STRING_TYPE === $v['type'] || Types\Type::TEXT_TYPE === $v['type'])
                    && ($searchable || ($isChar = isset($v['options']) && isset($v['options']['fixed'])))
                ) {
                    $predicateSet->or;
                    isset($isChar) && $isChar
                        ? $predicateSet->equalTo($k, $equalValue)
                        : $predicateSet->like($k, $likeValue);

                    if (CHAOS_MAX_QUERY <= ++$count) {
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
     * Returns the string $value, converting characters to their corresponding HTML entity equivalents where they exist.
     *
     * @param   string $value The value.
     * @param   boolean $checkDate [optional].
     * @return  string
     */
    public function filter($value, $checkDate = false)
    {
        if (isBlank($value) || !is_scalar($value)) {
            return '';
        }

        $value = trim($value);

        if (false !== $checkDate && 0 !== preg_match(CHAOS_MATCH_DATE, $value, $matches)) {
            $filtered = date(
                $this->getVars()->get('app.date_format'),
                is_bool($checkDate) ? strtotime($matches[0]) : strtotime($matches[0]) + $checkDate
            );
        } else {
            $filtered = StaticFilter::execute(
                $value, 'HtmlEntities', ['encoding' => $this->getVars()->get('app.charset')]
            );
        }

        return $filtered;
    }
}
