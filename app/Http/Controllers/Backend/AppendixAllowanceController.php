<?php

namespace App\Http\Controllers\Backend;

use App\Models\AppendixAllowance;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AppendixAllowanceController extends Controller
{

    public function ajaxStore(Request $request)
    {
    	if ($request->ajax()) {
			$input = $request->all();
			$allowance_cat = $input['appendix_allowance_cat_new'];
			$allowance_cat = array_values($allowance_cat);
			$allowance_desc = array_values($input['appendix_allowance_desc']);
			if (!$allowance_cat[0]) {
				$message = trans('Vui lòng chọn loại phụ cấp.');
				return response()->json([
					'message' => $message,
					'data' => ''
				]);
			}
			$allowance_cost =  array_values($input['appendix_allowance_cost_new']);
			if (count(array_unique($allowance_cat)) != count($allowance_cat)) {
				$message = trans('contracts.validate_allowance_category');
				return response()->json([
					'message' => $message,
					'data' => ''
				]);
			}
			if (strpos($input['code_new'], ' ')) {
				$message = trans('contracts.validate_space_code');
				return response()->json([
					'message' => $message,
					'data' => ''
				]);
			}
			$contract = Contract::find($input['contract_id']);
//			$appendixNames = array_unique($contract->appendixAllowances3->pluck('code')->toArray());
//			if (in_array($input['code_new'], $appendixNames)) {
//				$message = trans('appendixes.validate_appendix_allowance_name');
//				return response()->json([
//					'message' => $message,
//					'data' => '',
//					'errClass' => 'setBorderColor'
//				]);
//			}
			$count = count($allowance_cat);
			$appendixAllowances = [];
			$code_diff = now()->timestamp;
			for ($i = 0; $i < $count; $i++) {
				if (empty($allowance_cat[$i])) {
					$message = trans('Vui lòng chọn loại phụ cấp!');
					return response()->json([
						'message' => $message,
						'data' => ''
					]);
				}
				if (empty($allowance_cost[$i])) {
					$message = trans('contracts.validate_allowance_cost');
					return response()->json([
						'message' => $message,
						'data' => ''
					]);
				}

				$appendixAllowances[$i]['code_global'] = $input['code_new'];
				$appendixAllowances[$i]['allowance_id'] = $allowance_cat[$i];
				$appendixAllowances[$i]['expense'] = str_replace(',', '', $allowance_cost[$i]);
				$appendixAllowances[$i]['salary'] = $input['salary_new'] ? str_replace(',', '', $input['salary_new']) : null;
				$appendixAllowances[$i]['valid_from'] = $input['valid_from'];
				$appendixAllowances[$i]['valid_to'] = $input['valid_to'];
				$appendixAllowances[$i]['status'] = 1;
                $appendixAllowances[$i]['desc'] = $allowance_desc[$i];
                $appendixAllowances[$i]['code'] = $code_diff;
			}
			try {
				DB::beginTransaction();
				$contract->appendixAllowances3()->createMany($appendixAllowances);
				for ($i = 0; $i < $count; $i++) {
					Contract::setActiveAllowance($input['contract_id'], $allowance_cat[$i], $code_diff);
				}
				if ($input['salary_new']) Contract::setActiveSalary($input['contract_id'],$code_diff);
				DB::commit();
				$message = trans('system.success');
				return response()->json([
					'data' => true,
					'message' => $message,
				]);
			} catch (\Exception $e) {
				DB::rollBack();
				$message = trans('system.error_create');
				return response()->json([
					'message' => $message,
					'error' => $e,
					'data' => ''
				]);
			}
		}
    }

    public function ajaxUpdate(Request $request)
    {
		if ($request->ajax()) {
			$status = 1;
			$input = $request->all();
//			dd($input);
			$allowance_cat = reset( $input['appendix_allowance_cat']);
			$allowance_cost = reset($input['appendix_allowance_cost']);
			$desc = reset($input['appendix_allowance_desc']);
			if (count(array_unique($allowance_cat)) != count($allowance_cat)) {
				$message = trans('contracts.validate_allowance_category');
				return response()->json([
					'message' => $message,
					'data' => ''
				]);
			}
			$contract = Contract::find($input['contract_id']);
			$oldCode = $input['old_code'];
			$code = reset($input['code']);
			$salary = reset($input['salary']);
			$appendixCodes = array_unique($contract->appendixAllowances3->pluck('code')->toArray());
			unset($appendixCodes[array_search($oldCode, $appendixCodes)]);
			if (in_array($code, $appendixCodes)) {
				$message = trans('appendixes.validate_appendix_allowance_name');
				return response()->json([
					'message' => $message,
					'data' => '',
					'errClass' => 'setBorderColor'
				]);
			}
			$count = count($allowance_cat);
			$appendixAllowances = [];
			for ($i = 0; $i < $count; $i++) {
				if (empty($allowance_cost[$i])) {
					$message = trans('contracts.validate_allowance_cost');
					return response()->json([
						'message' => $message,
						'data' => ''
					]);
				}

				$appendixAllowances[$i]['code'] = $code;
				$appendixAllowances[$i]['allowance_id'] = $allowance_cat[$i];
				$appendixAllowances[$i]['expense'] = str_replace(',', '', $allowance_cost[$i]);
				$appendixAllowances[$i]['salary'] = str_replace(',', '', $salary);
				$appendixAllowances[$i]['valid_from'] = reset($input['valid_from']);
				$appendixAllowances[$i]['valid_to'] = reset($input['valid_to']);
				$appendixAllowances[$i]['desc'] = $desc[$i];
				$appendixAllowances[$i]['status'] = $status;
			}
			try {
				DB::beginTransaction();
				$contract->appendixAllowances3()->where('code', $oldCode)->delete();
				$contract->appendixAllowances3()->where('code', $oldCode)->createMany($appendixAllowances);
				DB::commit();
				$message = trans('system.success');
				return response()->json([
					'data' => true,
					'message' => $message,
				]);
			} catch (\Exception $e) {
				DB::rollBack();
				$message = trans('system.error_update');
				return response()->json([
					'message' => $message,
					'error' => $e,
					'data' => ''
				]);
			}
		}
    }

	public function ajaxDestroy(Request $request)
	{
		if ($request->ajax()) {
			$data= AppendixAllowance::where('code', $request->input('code'))->delete();
			if ($data) {
				$message = trans('system.success');
				return response()->json([
					'data' => $data,
					'message' => $message,
				]);
			} else {
				$message = trans('system.error_delete');
				return response()->json([
					'message' => $message,
					'data' => ''
				]);
			}
		}
	}
}
