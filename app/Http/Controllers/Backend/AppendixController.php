<?php

namespace App\Http\Controllers\Backend;

use App\Models\Appendix;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class AppendixController extends Controller
{
    public function index(Request $request)
    {
		$query = '1=1';
		$name = $request->input('name');
		$status     = intval($request->input('status', -1));
		$date_range = $request->input('date_range');
		$page_num   = intval($request->input('page_num', \App\Define\Constant::PAGE_NUM_20));
		if ($name) $query .= " AND name like '%" . $name . "%'";
		if ($status <> -1) $query .= " AND status = {$status}";
		if ($date_range) {
			$date_range = explode(' - ', $date_range);
			if (isset($date_range[0]) && isset($date_range[1])) {
				$query .= " AND created_at >= '" . date("Y-m-d 00:00:00", strtotime(str_replace('/', '-', ($date_range[0] == '' ? '1/1/2015' : $date_range[0]) ))) . "' AND updated_at <= '" . date("Y-m-d 23:59:59", strtotime(str_replace('/', '-', ($date_range[1] == '' ? date("d/m/Y") : $date_range[1]) ))) . "'";
			}
		}
    	$appendixes = Appendix::whereRaw($query)->orderBy('id', 'desc')->paginate($page_num);
        return view('backend.appendixes.index', compact('appendixes'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        if ($request->ajax()) {
			$request->merge(['status' => $request->input('status', 1)]);
        	$input = $request->only(['name', 'expense', 'description', 'status']);
			$input['expense'] = str_replace(',', '', $input['expense']);
			$validator = \Validator::make($input, Appendix::rules());
			$validator->setAttributeNames(trans('appendixes'));
			if ($validator->fails()) {
				return response()->json([
					'errors' => $validator->errors()
				]);
			}
			$data = Appendix::create($input);
			if ($data) {
				$message = trans('system.success');
				return response()->json([
					'data' => $data,
					'message' => $message,
				]);
			} else {
				$message = trans('system.have_an_error');
				return response()->json([
					'message' => $message,
					'data' => ''
				]);
			}
		} else {
			$message = trans('system.have_an_error');
			$statusCode = 405;
			return response()->json([
				'statusCode' => $statusCode,
				'message' => $message,
				'data' => ''
			]);
		}
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $appendix = Appendix::find($id);
        return response()->json(['data' => $appendix]);
    }

    public function update(Request $request, $id)
    {
		if ($request->ajax()) {
			$appendix = Appendix::find($id);
			$request->merge(['status' => $request->input('status', 1)]);
			$input = $request->only(['name', 'expense', 'description', 'status']);
			$input['expense'] = str_replace(',', '', $input['expense']);
			$validator = \Validator::make($input, Appendix::rules($request->id));
			$validator->setAttributeNames(trans('appendixes'));
			if ($validator->fails()) {
				return response()->json([
					'errors' => $validator->errors()
				]);
			}
			$data =$appendix->update($input);
			if ($data) {
				$message = trans('system.success');
				return response()->json([
					'data' => $data,
					'message' => $message,
				]);
			} else {
				$message = trans('system.have_an_error');
				return response()->json([
					'message' => $message,
					'data' => ''
				]);
			}
		} else {
			$message = trans('system.have_an_error');
			$statusCode = 405;
			return response()->json([
				'statusCode' => $statusCode,
				'message' => $message,
				'data' => ''
			]);
		}
    }

    public function destroy($id)
    {
        $appendix = Appendix::find($id);
        if ($appendix->contracts()->count()) {
			Session::flash('message', trans('appendixes.err_delete'));
			Session::flash('alert-class', 'danger');
			return redirect()->route('admin.appendixes.index');
		}
        $appendix->delete();
		Session::flash('message', trans('system.success'));
		Session::flash('alert-class', 'success');
		return redirect()->route('admin.appendixes.index');
    }
}
