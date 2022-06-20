<?php

namespace App\Defines;

class Appendix
{
    const CHANGE_ALLOWANCE = 1;
    const CONTRACT_OF_RESERVATION = 2;

    public static function getAppendixForOption()
    {
        return [self::CHANGE_ALLOWANCE => trans('appendixes.types.' . self::CHANGE_ALLOWANCE),
			self::CONTRACT_OF_RESERVATION => trans('appendixes.types.' . self::CONTRACT_OF_RESERVATION)];
    }
}