<?php

namespace App\Http\Controllers\Backend;

use App\Models\ListLog;
use App\StaffDayOff;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ListLogController extends Controller
{
    public function showLog(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        $statusCode = 400;
        if ($request->ajax()) {
            try {
                $lang = 'schedules.logs';
                $modelName = $request->modelName ? trim($request->modelName) : StaffDayOff::class;
                $objectId = intval($request->id);
                $record = StaffDayOff::find($objectId);
                if (is_null($record)) {
                    throw new \Exception($response['message'], 1);
                }
                $listUserId = [$record->created_by, $record['user_id']];
                $data = [];
                $logs = ListLog::where('object_type', StaffDayOff::class)
                    ->where('object_id', $objectId)
                    ->orderBy('action_at', 'desc')
                    ->get()
                    ->groupBy('field');
                //dd($logs->toArray());
                foreach ($logs as $field => $items) {
                    foreach ($items as $item) {
                        $listUserId[] = $item->action_by;
                        $data[$field][] = [
                            'old_data' => $item->old_data,
                            'new_data' => $item->new_data,
                            'action_by' => $item->action_by,
                            'action_at' => $item->action_at,
                        ];
                    }
                }
                $users = User::whereIn('id', $listUserId)->pluck('fullname', 'id')->toArray();

                $response['data'] = view('backend.pages._log_timeline', compact('data', 'record', 'users', 'lang'))->render();
                $response['message'] = trans('system.success');
                $statusCode = 200;
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage().$e->getLine();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 500;
            return response()->json($response, $statusCode);
        }
    }
}
