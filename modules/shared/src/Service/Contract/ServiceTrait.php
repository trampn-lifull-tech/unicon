<?php

namespace Chaos\Common\Service\Contract;

use Carbon\Carbon;
use Zend\Filter\StaticFilter;

/**
 * Class ServiceTrait
 * @author ntd1712
 *
 * @deprecated
 */
trait ServiceTrait
{
    /**
     * @var bool A value that indicates whether the transaction is enabled.
     */
    public $enableTransaction = false;

    /**
     * Returns the string $value, converting characters to their corresponding HTML entity equivalents where they exist.
     *
     * @param   string $value The value.
     * @param   bool $checkDate [optional].
     * @return  string
     */
    public function filter($value, $checkDate = false)
    {
        if (isBlank($value) || !is_scalar($value)) {
            return '';
        }

        $value = trim($value);

        if (false !== $checkDate && false !== ($time = strtotime($value))) {
            $carbon = Carbon::createFromTimestamp($time, $this->getVars()->get('app.timezone'));

            if (is_int($checkDate)) {
                $carbon->addSeconds($checkDate);
            }

            $filtered = $carbon->toDateTimeString();
        } else {
            $filtered = StaticFilter::execute(
                $value, 'HtmlEntities', ['encoding' => $this->getVars()->get('app.charset')]
            );
        }

        return $filtered;
    }
}
