<?php

namespace Chaos\Foundation\Types;

use Chaos\Foundation\Exceptions\ConversionException;

/**
 * Class DateTimeType
 * @author ntd1712
 *
 * @see \Doctrine\DBAL\Types\DateTimeType
 */
class DateTimeType extends Type
{
    /**
     * {@inheritdoc} @override
     *
     * <code>
     * $value = Type::getType(Type::DATETIME_TYPE)->convertToPHPValue($value);
     *
     * // $value = '2018-05-30T09:44:46.017'; // return '2018-05-30 09:44:46.017000';
     * //          '2018-05-30T09:44:46';     //        '2018-05-30 09:44:46.000000';
     * //          '2018-05-30T09:44';        //        '2018-05-30 09:44:00.000000';
     * //          '2018-05-30T09';           //        '2018-05-30 09:00:00.000000';
     * //          '2018-05-30';              //        '2018-05-30 00:00:00.000000';
     * //          '2018-05';                 //        '2018-05-01 00:00:00.000000';
     * //          '2018';                    //        '2018-06-11 20:18:00.000000';
     * </code>
     *
     * @param   mixed $value The value to convert.
     * @return  null|\DateTime
     */
    public function convertToPHPValue($value)
    {
        if (null === $value || $value instanceof \DateTime) {
            return $value;
        }

        $format = $this->__getConfig()->get('framework.datetime_format') ?: parent::DATETIME_FORMAT;
        $val = \DateTime::createFromFormat($format . '+', $value);

        if (!$val) {
            if (false !== strpos($value, 'T') && false === strpos($value, ':')) {
                $value .= ':00';
            }

            $val = date_create($value);
        }

        if (!$val) {
            throw ConversionException::conversionFailedFormat($value, $this, $format);
        }

        return $val;
    }
}
