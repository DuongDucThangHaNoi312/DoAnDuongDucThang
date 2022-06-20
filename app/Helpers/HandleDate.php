<?php


namespace App\Helpers;


use Carbon\Carbon;

class HandleDate
{
	static function compareDate($dateOne, $dateTwo) //format dd/mm/yyyy
	{
		return strtotime(str_replace('/', '-', $dateOne)) > strtotime(str_replace('/', '-', $dateTwo));
	}

	static function formatDate($date) // Y-m-d
	{
		return Carbon::parse($date)->format('d/m/Y');
	}

	static function formatDateDMY($date) //d/m/Y => ddmmYY
    {
        return date('dmy', strtotime(str_replace('/', '-', $date)));
    }
}