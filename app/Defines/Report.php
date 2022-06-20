<?php

namespace App\Define;

class Report
{
    const STAFF_DEPENDENCY          = 'STAFF_DEPENDENCY';
    const STAFF_SOCIAL_INSURANCE    = 'STAFF_SOCIAL_INSURANCE';
    const STAFF_LEAVE               = 'STAFF_LEAVE';
    const STAFF_KPI                 = 'STAFF_KPI';
    const CONTRACT                  = 'CONTRACT';
    const LEAVE_TYPE                = ['DETAIL', 'GENERAL'];

    public static function getAllReportsForOption()
    {
        return [self::STAFF_DEPENDENCY => trans('reports.types.' . self::STAFF_DEPENDENCY),
//                self::STAFF_SOCIAL_INSURANCE => trans('reports.types.' . self::STAFF_SOCIAL_INSURANCE),
                self::STAFF_LEAVE => trans('reports.types.' . self::STAFF_LEAVE),
                self::STAFF_KPI => trans('reports.types.' . self::STAFF_KPI),
                self::CONTRACT => trans('reports.types.' . self::CONTRACT),
        ];
    }

    const OPERATOR_GREATER_EQUAL= ">=";
    const OPERATOR_EQUAL        = "=";
    const OPERATOR_GREATER      = ">";
    const OPERATOR_LITTER_EQUAL = "<=";
    const OPERATOR_LITTER       = "<";

    public static function getOperators()
    {
        return [self::OPERATOR_GREATER_EQUAL => self::OPERATOR_GREATER_EQUAL, self::OPERATOR_EQUAL => self::OPERATOR_EQUAL, self::OPERATOR_GREATER => self::OPERATOR_GREATER, self::OPERATOR_LITTER_EQUAL => self::OPERATOR_LITTER_EQUAL, self::OPERATOR_LITTER => self::OPERATOR_LITTER];
    }

}