<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Newborn;
use App\StaffFamily;
use App\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class NewbornController extends Controller
{
    public function index()
    {
        $newborns = Newborn::whereNull('deleted_at')->get();

        return view('backend.newborn.index', compact('newborns'));
    }

    public function create()
    {
        $time = 7;
        // $note = 'Làm việc ' . $time . 'h/ngày, thời gian còn lại tính OT';
        $note = '';
        return view('backend.newborn.create', compact('time', 'note'));
    }

    public function edit($id)
    {
        $newborn = Newborn::find(intval($id));
        if (is_null($newborn)) {
            Session::flash('message', trans('system.have_an_error'));
			Session::flash('alert-class', 'danger');
			
            return redirect()->route('admin.newborns.index');
        }
        // if (!$newborn->note) {
        //     $time = 7;
        //     // $note = 'Làm việc ' . $time . 'h/ngày, thời gian còn lại tính OT';
        // } else {
        //     $time = $newborn->time;
        //     // $note = $newborn->note;
        // }
        $time = $newborn->time;
        $note = '';

        return view('backend.newborn.edit', compact('newborn', 'time', 'note'));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if (!is_null($data['start'])) $data['start'] = Carbon::createFromFormat('d/m/Y', $data['start'])->format('Y-m-d');
        if (!is_null($data['end'])) $data['end'] = Carbon::createFromFormat('d/m/Y', $data['end'])->format('Y-m-d');
        $data['created_by'] = Auth::user()->id;

        if (strtotime($data['start']) > strtotime($data['end'])) {
            return response()->json(['status' => 400, 'message' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu']);
        }

        $newborn = Newborn::where('user_id', $data['user_id'])->where('end', '>', $data['start'])->whereNull('deleted_at')->orderBy('created_at', 'DESC')->first();
        if (!is_null($newborn)) return response()->json(['status' => 400, 'message' => 'Nhân viên vẫn trong thời gian hưởng chế độ con nhỏ']);
        
        try {
            Newborn::create($data);

            return response()->json(['status' => 200, 'message' => 'Tạo mới thành công', 'link' => route('admin.newborns.index')]);

        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => 'Thêm mới thất bại']);
            dd($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        if ($request->ajax()) {
            $data = $request->all();
            $newborn = Newborn::find(intval($id));

            if (is_null($newborn)) {
                return response()->json(['status' => 400, 'message' => 'Có lỗi xảy ra']);
            }

            if (!is_null($data['start'])) $data['start'] = Carbon::createFromFormat('d/m/Y', $data['start'])->format('Y-m-d');
            if (!is_null($data['end'])) $data['end'] = Carbon::createFromFormat('d/m/Y', $data['end'])->format('Y-m-d');
            $data['created_by'] = Auth::user()->id;

            if (strtotime($data['start']) > strtotime($data['end'])) {
                return response()->json(['status' => 400, 'message' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu']);
            }
            
            try {
                $newborn->update($data);
                return response()->json(['status' => 200, 'message' => 'Sửa thành công']);

            } catch (Exception $e) {
                return response()->json(['status' => 400, 'message' => $e->getMessage()]);

            }
        }
    }

    public function searchUser(Request $request)
    {
        if ($request->ajax()) {
            if (!empty($request->q)) {
                $users = User::where('active', 1)->where('fullname', 'LIKE', '%' . $request->q . '%')->where('gender', 0)->whereNotIn('id', [1])->selectRaw('id, CONCAT(fullname, " - " ,code) as text')
                                    ->paginate(10)->toArray();
            } else {
                $users = User::where('active', 1)->where('gender', 0)->whereNotIn('id', [1])->selectRaw('id, CONCAT(fullname, " - " ,code) as text')
                                    ->paginate(10)->toArray();
            }
            
            if (count($users) > 0) return response()->json($users, 200);

            return response()->json(['error' => 'error', 'status' => 404]);
        }
    }

    public function check(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            $message = [];
            $users = StaffFamily::where('staff_id', $data['userId'])->where('relationship', 'CH')->get();
            
            if (count($users) > 0) {
                foreach ($users as $user) {
                    $dob = new DateTime($user->dob);
                    $now = new DateTime();
                    $difference = $now->diff($dob);

                    if ($difference->y == 0) {
                        $age = 'Nhân viên có con ' . $difference->m . ' tháng ' . $difference->d . ' ngày ' . ' tính đến hiện tại';
                    } else {
                        $age = 'Nhân viên có con ' . $difference->y . ' tuổi ' . $difference->m . ' tháng ' . $difference->d . ' ngày ' . ' tính đến hiện tại';
                    }
                    
                    array_push($message, $age);
                }
            }

            if (count($message) > 0) {
                
                return response()->json(['status' => 200, 'message' => $message]);
            } else {

                return response()->json(['status' => 400, 'message' => 'Cảnh báo chưa có người phụ thuộc là con']);
            }
            
        }
    }

    public function destroy($id)
    {
        $newborn = Newborn::find(intval($id));
        if (is_null($newborn)) {
            Session::flash('message', trans('system.have_an_error'));
			Session::flash('alert-class', 'danger');
			
            return redirect()->route('admin.newborns.index');
        }

        $newborn->update([
            'deleted_at' => date('Y-m-d H:i:s')
        ]);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
			
        return redirect()->route('admin.newborns.index');
    }
}
