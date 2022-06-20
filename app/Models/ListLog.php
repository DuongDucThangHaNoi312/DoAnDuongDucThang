<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ListLog extends Model
{
    public $timestamps = false;
    const tableName = 'list_logs';
    protected $fillable = ["field", 'old_data', "note", "new_data", "action_at", "action_by", "object_type", "object_id", 'key'];

    /*public function setTableName($createdAt)
    {
        $date   = strtotime($createdAt);
        $subfix = date("Y_m_01", $date); // 1 thang 1 file
        self::existedTableBySubfix($subfix);
        $this->table = self::tableName . '_' . $subfix;
    }

    public static function existedTableBySubfix($subfix)
    {
        if (!Cache::get('list_logs_' . $subfix)) {
            $existed = DB::select("SHOW TABLES LIKE '" . self::tableName . '_' . $subfix . "'");
            if (empty($existed)) {
                DB::statement("CREATE TABLE " . self::tableName . '_' . $subfix . " LIKE " . self::tableName);
            }
            Cache::forever('list_logs_' . $subfix, 1);
        }
        return true;
    }*/

    public function listLogable()
    {
        return $this->morphTo();
    }

}
