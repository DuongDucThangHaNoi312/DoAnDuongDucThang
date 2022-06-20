<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UnionFund;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;

class UnionFundController extends Controller
{
    public function index()
    {
        $items = UnionFund::whereNull('deleted_at')->get();
        return view('backend.union.index', compact('items'));
    }

    public function create()
    {
        return view('backend.union.create');
    }

    public function searchUser(Request $request)
    {
        if ($request->ajax()) {
            if (!empty($request->q)) {
                $users = User::where('active', 1)->where('fullname', 'LIKE', '%' . $request->q . '%')->whereNotIn('id', [1])->selectRaw('id, CONCAT(fullname, " - " ,code) as text')
                                    ->paginate(10)->toArray();
            } else {
                $users = User::where('active', 1)->whereNotIn('id', [1])->selectRaw('id, CONCAT(fullname, " - " ,code) as text')
                                    ->paginate(10)->toArray();
            }
            if (count($users) > 0) return response()->json($users, 200);

            return response()->json(['error' => 'error', 'status' => 404]);
        }
    }

    public function store(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            $insert = [];

            try {
                if (count($data['user_id']) > 0) {
                    foreach ($data['user_id'] as $key => $user) {
                        if ($user == null) continue;
                        $check = UnionFund::where('user_id', $user)->whereNull('deleted_at')->first();
                        if (!is_null($check)) return response()->json(['status' => 400, 'message' => 'Nhân viên đã tạo bản ghi']);

                        $date = str_replace('/', '-', $data['start']);
                        $insert[] = [
                            'user_id' => $user,
                            'start' => date('Y-m-d', strtotime($date)),
                            'month' => date('m', strtotime($date)),
                            'year' => date('Y', strtotime($date)),
                            'note'  => $data['note'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'created_by' => Auth::user()->id,
                            'deleted_at' => null
                        ];
                    }
                }

                UnionFund::insert($insert);

                return response()->json(['status' => 200, 'message' => 'Thêm mới thành công', 'link' => route('admin.unionfunds.index')]);
            } catch (Exception $e) {
                return response()->json(['status' => 400, 'message' => $e->getMessage()]);
            }
        }
    }

    public function edit($id)
    {
        $item = UnionFund::find(intval($id));
        if (is_null($item)) {
            Session::flash('message', trans('system.have_an_error'));
			Session::flash('alert-class', 'danger');
			
            return redirect()->route('admin.unionfunds.index');
        }

        return view('backend.union.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        if ($request->ajax()) {
            $item = UnionFund::find(intval($id));

            if (is_null($item)) {
                return response()->json(['status' => 400, 'message' => 'Không tìm thấy bản ghi']);
            }

            $data = $request->all();

            try {
                $date = str_replace('/', '-', $data['start']);
                $update = [
                    'start' => date('Y-m-d', strtotime($date)),
                    'month' => date('m', strtotime($date)),
                    'year' => date('Y', strtotime($date)),
                    'note'  => $data['note'],
                    'deleted_at' => null
                ];
                $item->update($update);
                return response()->json(['status' => 200, 'message' => 'Chỉnh sửa thành công', 'link' => route('admin.unionfunds.index')]);

            } catch (Exception $e) {
                return response()->json(['status' => 400, 'message' => $e->getMessage()]);
            }
        }
    }

    public function destroy($id)
    {
        $item = UnionFund::find(intval($id));
        if (is_null($item)) {
            Session::flash('message', trans('system.have_an_error'));
			Session::flash('alert-class', 'danger');
			
            return redirect()->route('admin.unionfunds.index');
        }
        
        try  {
            $item->update([
                'deleted_at' => date('Y-m-d H:i:s')
            ]);
            Session::flash('message', trans('system.success'));
            Session::flash('alert-class', 'success');
        } catch (Exception $e){
            Session::flash('message', $e->getMessage());
			Session::flash('alert-class', 'danger');
			
        }
			
        return redirect()->route('admin.unionfunds.index');
    }
}
