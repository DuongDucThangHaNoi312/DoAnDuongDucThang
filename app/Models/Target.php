<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    protected $table ='targets';
    protected $fillable =[
      'timestamp','user_id','kpi','created_by','description','created_at','updated_at', 'note', 'month', 'year'
    ];

    public static function rules($id = 0)
    {
        return [
            'user_id' => 'required',
            'kpi' => 'required|digits_between:0,200',
        ];
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getKpiUser($kpiData)
    {
        $result = [];
       /* foreach ($kpiData as $kpi) {
            $result[$kpi->user_id][$kpi->year][$kpi->month] = $kpi->kpi;
            $result[$kpi->user_id][$kpi->year]['total'] += $kpi->kpi;
            if ($kpi->kpi != null) $result[$kpi->user_id][$kpi->year]['countMonthAvg'] += 1;
        }*/
        foreach ($kpiData as $userId => $kpiMonths) {
            foreach ($kpiMonths as $month => $kpis) {
                $kpi = $kpis->max(); $year = $kpi->year; $kpiValue = $kpi->kpi;
                $result[$userId][$year][$month] = $kpiValue;
                $result[$userId][$year]['total'] += $kpiValue;
                if ($kpiValue !== null) $result[$userId][$year]['countMonthAvg'] += 1;
            }
        }
        return $result;
    }

    public static function getKpiUserPerMonth($staffId)
    {
        $y = date('Y');
        $kpiData = Target::where('user_id', $staffId)->where('year', $y)->get();
        $result = array_fill(0, 12, 0);
        if ($kpiData) {
            foreach ($kpiData as $kpi) {
                $key = $kpi->month - 1;
                $result[$key] = $kpi->kpi;
            }
        }
        return $result;
    }

    public function userBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public static function getKpiUserByMonthYear($userId, $m, $y)
    {
        $target = Target::where('user_id', $userId)->where('month', $m)->where('year', $y)->orderBy('id', 'DESC')->first();
        return $target ? $target->kpi : null;
    }
}
