<?php

namespace App\Http\Controllers\Backend;

use App\Defines\Staff;
use App\Models\CalendarDepartment;
use App\Models\Shift;
use App\Models\Shifts;
use App\Models\Team;
use App\StaffDayOff;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShiftsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $userLogin = Auth::user();
        $dayOffDepartments = CalendarDepartment::getDayOff($id);
        $dayOffDepartments = collect($dayOffDepartments);
        $typeDept = DB::table('departments')->where('id', $id)->first()->type;
        if (Auth::user()->hasRole('LEADER')) {
            $team = $userLogin->teamOfLead;
        }
        if ($userLogin->department_id = $id && count($team) > 0) {
            $users = $team->usersDetail->pluck('fullname', 'id')->toArray();
        } else {
            $users = User::where('department_id', $id)
                ->whereNotIn('fullname', Staff::USER_EXCEPT)
                ->whereNull('is_leave')
                ->where('active', 1)
                ->pluck('fullname', 'id')->toArray();
        }
        if ($userLogin->fullname != 'Admin')
        $users[Auth::user()->id] = Auth::user()->fullname;
        // unset($users[Auth::user()->id]);

        return view('backend.department.shift', compact('dayOffDepartments', 'typeDept', 'users', 'eventShifts', 'id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        $statusCode = 200;
        $shifts = \App\Define\Shift::getAllShift();
        $color = \App\Define\Shift::getColorShifts();
        if ($request->ajax()) {
            try {
                $event = $request->input('event');
                $event['color'] = $color[$event['shifts']];
                $event['title'] = $shifts[$event['shifts']];
                $today = Carbon::today();
                if (($event['shifts'] == ''))
                    throw new \Exception('Ca làm không để trống');
                $validator = \Validator::make($event, Shifts::rules());
                $validator->setAttributeNames(trans('shifts'));
                if ($validator->fails()) {
                    $errors = $validator->errors()->all();
                    throw new \Exception($errors[0]);
                }
                // if (strtotime($today) > strtotime($event['start'])) {
                //     $message = trans('schedules.error_past_day_off');
                //     throw new \Exception($message, 1);
                // }
                if (Carbon::parse($event['start'])->month != Carbon::parse($event['end'])->month) {
                    $message = trans('Chỉ sắp xếp lịch làm trong tháng');
                    throw new \Exception($message, 1);
                }
                $sameShift = Shifts::whereRaw("user_id = {$event['user_id']} AND ((start >= '{$event['start']}' AND start  <= '{$event['end']}') OR (end >= '{$event['start']}' AND end  <= '{$event['end']}') OR (start < '{$event['start']}' AND end > '{$event['end']}'))")
                    ->first();
                if (!is_null($sameShift))
                    throw new \Exception('Ngày chọn đã có ca làm');
                $event = Shifts::create($event);
                $response['message'] = trans('system.success');
                $response['data'] = $event;
                return response()->json($response, $statusCode);
            } catch (\Exception $e) {
                if ($statusCode == 200) $statusCode = 500;
                $response['message'] = $e->getMessage();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {

        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                $event = Shifts::find(intval($id));
                if (is_null($event)) {
                    $message = trans('system.no_record_found');
                    throw new \Exception($message, 1);
                }
                $today = Carbon::today();
                if (strtotime($today) > strtotime($event->start)) {
                    $message = trans('schedules.error_past_day_off');
                    throw new \Exception($message, 1);
                }
                $eventNew = $request->input('event');
                $duration = $request->input('duration');
                if ($duration) {
                    $eventNew = $event->toArray();
                    $eventNew['start'] = Carbon::createFromFormat('Y-m-d', $event['start'])->addDays($duration)->format('Y-m-d');
                    $eventNew['end'] = Carbon::createFromFormat('Y-m-d', $event['end'])->addDays($duration)->format('Y-m-d');
                }

                $sameShift = Shifts::whereRaw("id <> {$id} AND user_id = {$event['user_id']} AND ((start >= '{$event['start']}' AND start  <= '{$event['end']}') OR (end >= '{$event['start']}' AND end  <= '{$event['end']}') OR (start < '{$event['start']}' AND end > '{$event['end']}'))")
                    ->first();
                if (!is_null($sameShift))
                    throw new \Exception('Ngày chọn đã có ca làm');

                $eventNew['color'] = trans('shifts.color_types.' . $eventNew['shifts']);
                $eventNew['title'] = trans('shifts.shifts.' . $eventNew['shifts']);
                $event->update($eventNew);
                $response['message'] = trans('system.success');
                $response['data'] = $event;
            } catch (\Exception $e) {
                if ($statusCode == 200) $statusCode = 500;
                $response['message'] = $e->getMessage();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                $query = Shifts::where('id', $id);
                $dayOffEvent = $query->first();
                if (is_null($dayOffEvent)) {
                    $message = trans('system.no_record_found');
                    throw new \Exception($message, 1);
                }
                // $today = Carbon::today();
                // if (strtotime($today) > strtotime($dayOffEvent->start)) {
                //     $message = trans('shifts.error_past_day_off');
                //     throw new \Exception($message, 1);
                // }
                $query->forceDelete();
                $response['message'] = trans('system.success');
            } catch (\Exception $e) {
                if ($statusCode == 200) $statusCode = 500;
                $response['message'] = $e->getMessage();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function getShiftsUser(Request $request)
    {
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 200;
        if ($request->ajax()) {
            $departmentId = User::find($request->input('user_id'))->department_id;
            $eventShifts = Shifts::getShifts($request->input('user_id'));
            $userLogin = Auth::user();
            $team = $userLogin->teamOfLead;
            if ($userLogin->department_id = $departmentId && count($team) > 0) {
                $temp = $team->usersDetail;
                $users = count($temp) ? $temp->pluck('fullname', 'id')->toArray() : [];
            } else {
                $users = User::where('department_id', $departmentId)->pluck('fullname', 'id')->toArray();
            }

            unset($users[Auth::user()->id]);
            unset($users[$request->input('user_id')]);
            $response['data'] = collect($eventShifts);
            $response['user'] = collect($users);
            $response['message'] = trans('system.success');
            return response()->json($response, $statusCode);
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function copyShifts(Request $request)
    {
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 200;
        if ($request->ajax()) {
            $data = $request->all();
            Shifts::whereMonth('start', $data['month'])->whereYear('start', $data['year'])->whereMonth('end',$data['month'])->whereYear('end', $data['year'])->whereIn('user_id', $data['user_id_apply'])->delete();
            $copy = Shifts::where('user_id',$data['user_id'])->whereMonth('start', $data['month'])->whereYear('start', $data['year'])->whereMonth('end',$data['month'])->whereYear('end', $data['year'])->get();

            foreach ($copy as $dulicates) {
                foreach ($data['user_id_apply'] as $user_id) {
                    $dulicate = $dulicates->replicate();
                    $dulicate->user_id = $user_id;
                    $dulicate->save();
                }
            }

            $response['data'] = collect($copy);
            $response['message'] = trans('system.success');
            return response()->json($response, $statusCode);
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }
}
