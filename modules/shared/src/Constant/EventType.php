<?php

namespace Chaos\Common\Constant;

/**
 * Class EventType
 * @author ntd1712
 */
final class EventType extends Reflector
{
    const ON_AFTER_READ_ALL = 'onAfterReadAll';
    const ON_AFTER_READ = 'onAfterRead';
    const ON_EXCHANGE_ARRAY = 'onExchangeArray';
    const ON_VALIDATE = 'onValidate';
    const ON_BEFORE_SAVE = 'onBeforeSave';
    const ON_AFTER_SAVE = 'onAfterSave';
    const ON_BEFORE_DELETE = 'onBeforeDelete';
    const ON_AFTER_DELETE = 'onAfterDelete';
}
