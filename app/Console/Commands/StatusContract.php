<?php

namespace App\Console\Commands;

use App\Defines\Contract;
use App\Defines\Schedule;
use App\Models\ListLog;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatusContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contract:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /* Hợp đồng hết hạn:
         * Set status = 0
         * Set type_status thành nghỉ việc cho những hợp đồng trạng thái chờ nghỉ việc
         * Update 1 số trường ở bảng user
         * */
        $contracts = \App\Models\Contract::whereNotNull('set_notvalid_on')
            ->where('set_notvalid_on', '<=', now()->format('Y-m-d'))
            ->get();
        $_STATUS_CHO_NGHI_VIEC = Contract::CHO_NGHI_VIEC;
        $_STATUS_NGHI_VIEC = Contract::LEAVE_WORK;
        $_STATUS_ACTIVE = Contract::ACTIVE;
        $_STATUS_EXPIRED = Contract::EXPIRED;
        $_SALARY_END = Schedule::DATE_START_SALARY;
        $nowDate = now()->format('Y-m-d');
        $dataLogs = [];
        foreach ($contracts as $item) {
            $data = [];
            if ($item->type_status == $_STATUS_CHO_NGHI_VIEC) {
                $data['status'] = 0;
                $tz = strtotime($item->set_notvalid_on);
                //Update từng hợp đồng sẽ diễn ra vào đầu tháng sau theo ngày chấm dứt
                //Nếu nghỉ việc vào ngày > 26, thì update diễn ra vào đầu tháng sau nữa
                $day = date('d', $tz); $y = date('Y', $tz); $m = date('m', $tz);
                $addMonth = '+1 month';
                if ($day > $_SALARY_END) $addMonth = '+2 month';
                $dateUpdate = date('Y-m-d', strtotime($addMonth, strtotime($y.'-'.$m.'-'.'01')));
                if ($dateUpdate != $nowDate) continue;
                $data['type_status'] = $_STATUS_NGHI_VIEC;
                if (!\App\Models\Contract::where('user_id', $item->user_id)->where('type_status', 1)->first()) {
                    User::where('id', $item->user_id)
                        ->update(['active' => 0, 'is_leave' => 1]);
                }
            } elseif ($item->type_status == $_STATUS_ACTIVE && $item->is_main != 1) {
                if (is_null($item->type) || $item->type_status != $_STATUS_ACTIVE) continue;
                $data['status'] = 0;
                $data['type_status'] = $_STATUS_EXPIRED;
                if (!\App\Models\Contract::where('user_id', $item->user_id)->where('id', '<>', $item->id)->where('type_status', 1)->first()) {
                    User::where('id', $item->user_id)
                        ->update(['active' => 0]);
                }
            }
            if ($data) {
                $item->update($data);
                foreach ($data as $key => $v) {
                    $dataLogs[] = [
                        'new_data' => $v,
                        'old_data' => $item->$key,
                        'field'    => $key,
                        'action_at' => now(),
                        'action_by' => 1,
                        'key' => now()->timestamp,
                        'note' => 'cronjob',
                        'object_id'             => $item->id,
                        'object_type'           => \App\Models\Contract::class,
                    ];
                }
            }
        }

        /*
         * CHuyển trạng thái cho hợp đồng CHờ áp dụng
         * */

        $futureContracts = \App\Models\Contract::where('type_status', Contract::FUTURE)
            ->get();
        foreach ($futureContracts as $item) {
            if ($item->valid_from->format('Y-m-d') <= today()->format('Y-m-d')) {
                $d = [];
                $item->update(['type_status' => Contract::ACTIVE, 'is_used' => 1, 'status' => 1]);
                foreach (['type_status' => Contract::ACTIVE, 'is_used' => 1] as $key => $v) {
                    $dataLogs[] = [
                        'new_data' => $v,
                        'old_data' => $item->$key,
                        'field'    => $key,
                        'action_at' => now(),
                        'action_by' => 1,
                        'key' => now()->timestamp,
                        'note' => 'cronjob future',
                        'object_id'             => $item->id,
                        'object_type'           => \App\Models\Contract::class,
                    ];
                }
                $userT = User::where('id', $item->user_id)
                    ->first();
                if (!is_null($userT)) {
                    if ($userT->active != 1) $d['active'] = 1;
                    if ($userT->status != $item->is_main) $d['status'] = $item->is_main;
                    if ($userT->dept_group_id != $item->department_group_id) $d['dept_group_id'] = $item->department_group_id;
                    foreach (['company_id', 'department_id', 'position_id', 'qualification_id'] as $key) {
                        if ($userT->$key != $item->$key) $d[$key] = $item->$key;
                    }
                    if (!is_null($userT->is_leave)) $d['is_leave'] = null;
                    if ($d) {
                        $userT->update($d);
                    }
                }
            }
        }
        if ($dataLogs)
            ListLog::insert($dataLogs);
		$this->info('Successfully set status.');
    }
}
