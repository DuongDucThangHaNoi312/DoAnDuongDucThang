<?php

namespace App\Http\Controllers\Backend;

use App\District;
use App\Province;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class LocationsController extends Controller
{
    public function index(Request $request)
    {
        $query = "1=1";
        $page_num       = intval($request->input('page_num', \App\Define\Constant::PAGE_NUM_20));
        $status         = intval($request->input('status', -1));
        if($status <> -1) $query .= " AND status = {$status}";
        $provinces = Province::whereRaw($query)->orderBy('id', 'DESC')->paginate($page_num);

        return view('backend.locations.index', compact('provinces'));
    }

    public function show(Request $request, $id)
    {
        $id = intval($id);
        $province = Province::find($id);
        if (is_null($province)) {
            Session::flash('message', "Tỉnh/thành không hợp lệ.");
            Session::flash('alert-class', 'error');
            return back();
        }

        $showed = District::where('province_id', $id)->where('status', 1)->pluck('name', 'id')->toArray();
        $hidden = District::where('province_id', $id)->where('status', 0)->pluck('name', 'id')->toArray();

        return view('backend.locations.show', compact('showed', 'hidden', 'province'));
    }

    public function store(Request $request)
    {
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 200;
        if($request->ajax()) {
            try {
                $ids = json_decode($request->input('ids'));
                $province = Province::find(intval($request->province));
                if (is_null($province)) {
                    Session::flash('message', "Tỉnh/thành không hợp lệ.");
                    Session::flash('alert-class', 'error');
                    return back();
                }

                District::where('province_id', $province->id)->whereIn('id', $ids)->update(['status' => 1]);
                District::where('province_id', $province->id)->whereNotIn('id', $ids)->update(['status' => 0]);

                $response['message'] = route('admin.locations.index');
                Session::flash('message', trans('system.success'));
                Session::flash('alert-class', 'success');
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

    public function update(Request $request)
    {
        $response = [ 'success' => 0, 'message' => trans('system.have_an_error') ];
        $statusCode = 200;
        if($request->ajax()) {
            try {
                $request->merge(['status' => $request->input('status', 0)]);
                $province = Province::find(intval($request->province_id));
                if (is_null($province)) {
                    $statusCode = 404;
                    throw new \Exception(trans('system.no_record_found'));
                }
                $province->status = ($request->status == 'true' ? 1 : 0);
                $province->save();

                $response['success'] = 1;
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
}
