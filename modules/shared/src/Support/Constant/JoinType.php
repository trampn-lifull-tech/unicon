<?php

namespace Chaos\Support\Constant;

/**
 * Class JoinType
 * @author ntd1712
 */
final class JoinType extends Reflector
{
    const JOIN = 'join';
    const INNER_JOIN = 'innerJoin';

    const LEFT_JOIN = 'leftJoin';
    const LEFT_OUTER_JOIN = 'leftOuterJoin';

    const RIGHT_JOIN = 'rightJoin';
    const RIGHT_OUTER_JOIN = 'rightOuterJoin';

    const FULL_JOIN = 'fullJoin';
    const FULL_OUTER_JOIN = 'fullOuterJoin';
}
