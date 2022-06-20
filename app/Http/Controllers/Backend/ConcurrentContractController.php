<?php

namespace App\Http\Controllers\Backend;

use App\Helper\HString;
use App\Models\Contract;
use App\Helpers\HandleDate;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ConcurrentContract;
use App\Http\Controllers\Controller;

class ConcurrentContractController extends Controller
{
    public function ajaxUpdateOrCreate(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        $statusCode = 400;
        if ($request->ajax()) {
            try {
                $input = $request->all();
                if (empty($input['appendix_company_id']))
                    throw new \Exception(trans('appendixes.error_data'));
                $contract = Contract::find($input['contract_id']);
                if (is_null($contract))
                    throw new \Exception('Hợp đồng chính không tồn tại');
                $companyIds = $input['appendix_company_id'];
                if (in_array($contract->company_id, $companyIds))
                    throw new \Exception(trans('appendixes.error_validate_main_company_unique'));
                if (count(array_unique($companyIds)) != count($companyIds))
                    throw new \Exception('Không tạo nhiều kiêm nhiệm cùng công ty trong 1 hợp đồng chính');
                $count = count($companyIds);
                $concurrentContracts = [];
                //dd($input);
                //Tìm hd kiem nhiệm của người này ở hđ chính khác
                $oldConcurrentContracts = ConcurrentContract::with('company', 'department')
                    ->where('user_id', $contract->user_id)
                    ->where('contract_id', '<>', $contract->id)
                    ->get();
                    //->groupBy(DB::raw('CONCAT(company_id,department_id)'));
                DB::beginTransaction();
                for ($i = 0; $i < $count; $i++) {
                    $concurrentContracts[$i]['id'] = array_values($input['id'])[$i];
                    $concurrentContracts[$i]['company_id'] = array_values($companyIds)[$i];
                    $concurrentContracts[$i]['department_id'] = array_values($input['appendix_department_id'])[$i];
                    $concurrentContracts[$i]['position_id'] = array_values($input['appendix_position_id'])[$i];
                    $concurrentContracts[$i]['qualification_id'] = array_values($input['appendix_qualification_id'])[$i];
                    $concurrentContracts[$i]['salary'] = str_replace(',', '', array_values($input['salary'])[$i]);
                    $concurrentContracts[$i]['valid_from'] = array_values($input['appendix_valid_from'])[$i];
                    $concurrentContracts[$i]['valid_to'] = array_values($input['appendix_valid_to'])[$i];
                    $concurrentContracts[$i]['status'] = 1;
                    $concurrentContracts[$i]['contract_id'] = $input['contract_id'];
                    foreach ($oldConcurrentContracts as $item) {
                        if ($concurrentContracts[$i]['department_id'].$concurrentContracts[$i]['company_id'] == $item->department_id.$item->company_id) {
                            if (HString::checkTwoRangeDateOverlap($concurrentContracts[$i]['valid_from'], $concurrentContracts[$i]['valid_to'], date('d/m/Y', strtotime($item->valid_from)), date('d/m/Y', strtotime($item->valid_to)))) {
                                $from = date('d/m/Y', strtotime($item->valid_from));
                                $to = date('d/m/Y', strtotime($item->valid_to));
                                throw new \Exception("Nhân viên đang có kiêm nhiệm tại {$item->department->name}-{$item->company->shortened_name} từ {$from} đến {$to}");
                            }
                        }
                    }
                    $validator = \Validator::make($concurrentContracts[$i], ConcurrentContract::rules($input['contract_id'], array_values($input['id'])[$i]), [
                        // 'company_id.unique' => trans('contracts.company_unique_concurrent')
                    ]);
                    $validator->setAttributeNames(trans('contracts'));
                    if ($validator->fails()) {
                        $errors = $validator->errors()->all();
                        throw new \Exception($errors[0]);
                    }
                    if (!HandleDate::compareDate($concurrentContracts[$i]['valid_to'], $concurrentContracts[$i]['valid_from']))
                        throw new \Exception(trans('contracts.validate_valid_to'));
                    if ($contract->valid_to) {
                        if (HandleDate::compareDate($concurrentContracts[$i]['valid_to'], $contract->valid_to)) {
                            throw new \Exception(trans('contracts.validate_valid_to2'));
                        }
                    }
                    ConcurrentContract::updateOrCreate(
                        [
                            'id' => $concurrentContracts[$i]['id']
                        ],
                        [
                            'contract_id' => $concurrentContracts[$i]['contract_id'],
                            'company_id' => $concurrentContracts[$i]['company_id'],
                            'department_id' => $concurrentContracts[$i]['department_id'],
                            'position_id' => $concurrentContracts[$i]['position_id'],
                            'qualification_id' => $concurrentContracts[$i]['qualification_id'],
                            'salary' => empty($concurrentContracts[$i]['salary']) ? 0 : $concurrentContracts[$i]['salary'],
                            'valid_from' => $concurrentContracts[$i]['valid_from'],
                            'valid_to' => $concurrentContracts[$i]['valid_to'],
                            'status' => $concurrentContracts[$i]['status'],
                            'user_id' => $contract->user_id,
                            'created_by' => Auth::id(),
                            'department_group_id' => Department::getGroupOfDept($concurrentContracts[$i]['department_id'])
                        ]
                    );
                }
                DB::commit();
                $response['message'] = trans('system.success');
                $statusCode = 200;
			} catch (\Exception $e) {
				DB::rollBack();
                $response['message'] = $e->getMessage();
			} finally {
                return response()->json($response, $statusCode);
            }
		} else {
            return response()->json($response, $statusCode);
        }
    }

    public function ajaxDestroy(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                $id = $request->input('id');
                $concurrentContract = ConcurrentContract::find($id);
                if (is_null($concurrentContract)) {
                    $message = trans('system.null_data');
                    throw new \Exception($message, 1);
                }
                $concurrentContract->delete();
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
