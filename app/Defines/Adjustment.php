<?php

namespace App\Defines;

class Adjustment
{
    const  INCREASE_ADJUSTMENT = 1;
    const  REDUCE_ADJUSTMENT    = 2;
    const  TAXABLE              =1;
    const  TAX_EXEMPTION        =2; 
    public static function getAdjustmentTypesForOption()
    {
        return [ self::INCREASE_ADJUSTMENT => trans('adjustments.adjustment_types.' . self::INCREASE_ADJUSTMENT), self::REDUCE_ADJUSTMENT => trans('adjustments.adjustment_types.' . self::REDUCE_ADJUSTMENT) ];
    }
    public static function getTaxStatusForOption()
    {
        return [ self::TAXABLE => trans('adjustments.tax_status.' . self::TAXABLE), self::TAX_EXEMPTION => trans('adjustments.tax_status.' . self::TAX_EXEMPTION) ];
    }
}