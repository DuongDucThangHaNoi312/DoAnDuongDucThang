<?php

namespace App\Http\Controllers\Backend;

use App\Role;
use App\User;
use App\Bank;
use App\Staff;
use Validator;
use App\Permission;
use App\StaffDayOff;
use App\StaffFamily;
use App\Models\Team;
use App\Models\Shifts;
use App\PermissionUser;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Department;
use Illuminate\Http\Request;
use App\PermissionUserObject;
use Illuminate\Support\Carbon;
use App\Models\DepartmentGroup;
use App\Traits\StorageImageTraits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class StaffsController extends Controller
{
    use StorageImageTraits;

    public function index(Request $request)
    {   
        $users = User::with('department')->get();
        return view('backend.staffs.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::find(intval($id));
        $departments = Department::pluck("name", "id")->toArray();
        if (is_null($user)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.staffs.index');
        }
        return view('backend.staffs.show', compact('user', 'departments'));
    }


    public function create(Request $request)
    {
        $departments = Department::pluck("name", "id")->toArray();
        return view('backend.staffs.create', compact('roles', 'departments'));
    }

    public function store(Request $request)
    {
        $request->merge(['active' => intval($request->input('active', 1))]);
        $validator = \Validator::make($data = $request->all(), User::rules());
        $validator->setAttributeNames(trans('staffs'));
        if ($validator->fails()) return back()->withErrors($validator)->withInput();

        $arrData = ([
            'code' => $request->code,
            'id_card_no' => $request->id_card_no,
            'issued_on' => Carbon::createFromFormat('d/m/Y', $request->issued_on)->format('Y-m-d'),
            'issued_at' => $request->issued_at,
            'addresses' => $request->addresses,
            'phone' => $request->phone,
            'fullname' => $request->fullname,
            'activated' => 1,
            'active' => $request->active,
            'date_of_birth' => Carbon::createFromFormat('d/m/Y', $request->date_of_birth)->format('Y-m-d'),
            'gender' => $request->gender,
            'nationality' => $request->nationality,
            'email' => $request->email,
            'password' => bcrypt('123@123'),
            'created_by' => auth()->id(),
            'department_id' => $request->deparment,
            'ethnicity' => $request->ethnicity,
            'domicile' => $request->domicile,
            'marital_status' => $request->marital_status,
            'admin' => intval($request->admin),
            
            
        ]);
        $user = User::create($arrData);
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.staffs.index');
    }

    public function edit($id)
    {
        $user = User::find(intval($id));
        $departments = Department::pluck("name", "id")->toArray();
        if (is_null($user)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.staffs.index');
        }
        return view('backend.staffs.edit', compact('user', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $user = User::find(intval($id));
        if (is_null($user)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.staffs.index');
        }
        $request->merge(['active' => $request->input('active', 0)]);
        $validator = \Validator::make($data = $request->only(['status', 'active', 'fullname', 'date_of_birth', 'nationality', 'code', 'gender', 'id_card_no', 'issued_at', 'issued_on', 'addresses', 'activated', 'phone', 'code_timekeeping', 'email', 'tax_code', 'insurance_no', 'bank_name', 'bank_account', 'driver_license_no', 'driver_license_class', 'driver_license_expire', 'family_relationship_id', 'family_relationship', 'family_fullname', 'family_dob', 'family_dependent', 'family_dependent_from', 'family_dependent_to', 'family_gender', 'family_tax_code', 'emergency_phone', 'emergency_contact', 'code_timekeeping_subs']), User::rules(intval($id)));
        $validator->setAttributeNames(trans('staffs'));
        if ($validator->fails()) return back()->withErrors($validator)->withInput();

        $arrData = ([
            'code' => $request->code,
            'id_card_no' => $request->id_card_no,
            'issued_on' => date("Y-m-d 00:00:00", strtotime(str_replace('/', '-', $request->issued_on))),
            'issued_at' => $request->issued_at,
            'addresses' => $request->addresses,
            'phone' => $request->phone,
            'fullname' => $request->fullname,
            'activated' => 1,
            'active' => $request->active,
            'date_of_birth' => date("Y-m-d 00:00:00", strtotime(str_replace('/', '-', $request->date_of_birth))),
            'gender' => $request->gender,
            'nationality' => $request->nationality,
            'email' => $request->email ?? $user->email,
            'department_id' => $request->deparment,
            'ethnicity' => $request->ethnicity,
            'domicile' => $request->domicile,
            'marital_status' => $request->marital_status,
            'admin' => intval($request->admin),

        ]);
        $user->update($arrData);
      
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.staffs.index');
    }

    public function destroy($id)
    {
        $users = User::find(intval($id));
        if (is_null($users)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.staffs.index');
        }
    
        $users->delete();
        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.staffs.index');
    }

    public function showCalendar(Request $request, $id)
    {
        $data['staffs'] = Staff::find(intval($id));
        $data['month'] = \Carbon\Carbon::now()->month;
        return view('backend.staffs.calendar', $data);
    }

    public function leave(Request $request)
    {
        $page_num = intval($request->input('page_num', \App\Define\Constant::PAGE_NUM));
        $leaves = StaffDayOff::withTrashed()->where('user_id', Auth::id())->orderBy('updated_at', 'DESC')->paginate($page_num);

        return view('backend.staff-leaves.index', compact('leaves'));
    }

    public function destroys($id)
    {
        $leave = StaffDayOff::find(intval($id));
        if (is_null($leave) || now()->format('Y-m-d') > $leave->start) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.take-leave.staffs.index');
        }

        $leave->forceDelete();
        Session::flash('message', trans('schedules.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.take-leave.staffs.index');
    }

    public function action($id)
    {
        $staffs = StaffDayOff::find($id);
        if (is_null($staffs)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.take-leave.staffs.index');
        }
        $staffs->delete();
        Session::flash('message', trans('schedules.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.take-leave.staffs.index');
    }

    public function changePassword(Request $request, $id)
    {
        $id = intval($id);
        $staffs = User::where('id', $id)->first();
        if (is_null($staffs)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.staffs.index');
        }

        return view('backend.staffs.change-password', compact('staffs'));
    }

    public function postChangePassword(Request $request, $id)
    {
        $id = intval($id);
        $validator = \Validator::make($request->all(), array(
            'new_password' => 'required|min:6|max:30',
            're_password' => 'same:new_password',
        ));

        $validator->setAttributeNames(trans('users'));
        if ($validator->fails()) return back()->withErrors($validator)->withInput();

        $user = User::where('id', $id)->first();
        if (is_null($user)) {
            $errors = new \Illuminate\Support\MessageBag;
            $errors->add('editError', trans('system.have_an_error'));
            return back()->withErrors($errors)->withInput();
        }

        $user->password = \Hash::make($request->input('new_password'));
        $user->save();

        Session::flash('message', trans('system.success'));
        Session::flash('alert-class', 'success');
        return redirect()->route('admin.staffs.index');
    }

    public function userInfo($id)
    {
        $user = User::find($id);
        if (empty($user)) {
            return \Response::json([
                'status' => 'FAIL',
                'message' => trans('system.have_an_error')
            ]);
        }
        $user->load('company', 'department');
        $data = [
            'fullname' => $user->fullname,
            'code' => $user->code,
            'company_name' => !is_null($user->company->name) ? $user->company->name : '',
            'department_name' => !is_null($user->department->name) ? $user->department->name : '',
            'id_card_no' => $user->id_card_no,
            'issued_on' => date('d/m/Y', strtotime($user->issued_on)),
            'issued_at' => $user->issued_at,
            'address' => $user->addresses ?? '',
            'phone' => $user->phone,
            'email' => $user->email,
            'date_of_birth' => date('d/m/Y', strtotime($user->date_of_birth)),
            'gender' => $user->gender == 1 ? 'Nam' : 'Nữ',
            'code_timekeeping' => $user->code_timekeeping,
            'type_department' => $user->department->type ? trans('shifts.types.' . $user->department->type) : '',
            'position' => $user->position->name ?? '',
        ];

        return \Response::json([
            'status' => 'SUCCESS',
            'data' => $data
        ]);
    }

    public function createBulk(Request $request)
    {
        return view('backend.staffs.create_multi');
    }

    public function readBulk(Request $request)
    {
        ini_set('memory_limit', '4096M');
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                $file = $request->file;
                switch ($file->getClientOriginalExtension()) {
                    case 'xlsx':
                        $data = \Excel::toArray(new \App\Imports\StaffsImport, $file);
                        if ($data) $data = $data[0];
                        if (count($data) == 0 || !isset($data[0][0]) || count($data[0]) < 66) {
                            throw new \Exception("Không được sửa dòng đầu tiên của file mẫu nhập liệu", 1);
                        }
                        if (!isset($data[1][0])) {
                            throw new \Exception("File tải lên không có dữ liệu", 1);
                        }
                        $response['message'] = view('backend.staffs.excel', compact('data'))->render();
                        break;
                    default:
                        throw new \Exception("Không hỗ trợ định dạng", 1);
                }
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

    public function saveBulk(Request $request)
    {
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                $data = $request->data;
                if (!is_array($data) || count($data) == 0) {
                    $statusCode = 400;
                    throw new \Exception(trans('system.have_an_error'), 1);
                }
                $staffData = [];
                $existedCodes = User::pluck('code', 'code')->toArray();
                $existedEmails = User::pluck('email', 'email')->toArray();
                $banks = Bank::where('status', 1)->selectRaw("CONCAT(code, ' - ', name) as name, LOWER(code) as code")->pluck("name", "code")->toArray();
                foreach ($data as $d) {
                    if (!is_array($d) || count($d) <> 67) {
                        throw new \Exception("Dữ liệu dòng số " . ($d[0] ?? "") . " không đúng");
                    }
                    $code = trim($d[1]);
                    $fullname = trim($d[2]);
                    $email = trim($d[3]);
                    $dob = trim($d[7]);
                    $phone = trim($d[8]);
                    $gender = strtolower(\App\Helper\HString::removeVietnameseSign($d[9]));
                    $idNo = trim($d[10]);
                    $idIssueOfDate = trim($d[11]);
                    $idIssueOfPlace = trim($d[12]);
                    $maritalStatus = strtolower(\App\Helper\HString::removeVietnameseSign($d[13]));
                    $qualification = strtolower(\App\Helper\HString::removeVietnameseSign($d[15]));
                    $emergencyContact = trim($d[16]);
                    $emergencyPhone = trim($d[17]);
                    $bankName = strtolower(trim($d[21]));
                    $staffStart = trim($d[22]);
                    $codeTimekeeping = trim($d[23]);
                    $dlNo = trim($d[24]);
                    $dlClass = trim($d[25]);
                    $dlExpire = trim($d[26]);

                    $dop = trim($d[5]);
                    if ($code == "") {
                        throw new \Exception("Kiểm tra lại Mã nhân viên tại dòng số " . ($d[0] ?? "") . "");
                    }
                    if (isset($existedCodes[$code])) {
                        throw new \Exception("Mã nhân viên tại dòng số " . ($d[0] ?? "") . " đã có trên hệ thống");
                    }
                    if (isset($staffData[$code])) {
                        throw new \Exception("Mã nhân viên tại dòng số " . ($d[0] ?? "") . " đã có trên file excel trước đó");
                    }
                    if ($fullname == "") {
                        throw new \Exception("Kiểm tra lại Họ tên tại dòng số " . ($d[0] ?? "") . "");
                    }
                    if ($email == "") {
                        $email = time() . "-" . str_random(6) . '@company-domain.com';
                    }
                    if (isset($existedEmails[$email])) {
                        throw new \Exception("Email tại dòng số " . ($d[0] ?? "") . " đã có trên hệ thống");
                    }
                    if (strtotime(str_replace('/', '-', $dob)) == false) {
                        throw new \Exception("Kiểm tra lại Ngày sinh tại dòng số " . ($d[0] ?? "") . "");
                    }
                    $dob = Carbon::createFromFormat('d/m/Y', $dob)->format('Y-m-d');
                    if (!preg_match('/^0[0-9]{9}$/', $phone)) {
                        throw new \Exception("Kiểm tra lại Số điện thoại tại dòng số " . ($d[0] ?? "") . "");
                    }
                    if ($staffStart) {
                        $staffStart = strtotime(str_replace('/', '-', $staffStart));
                        if (!$staffStart) throw new \Exception("Kiểm tra ngày vào công ty tại dòng số " . ($d[0] ?? "") . "");
                        $staffStart = date('Y-m-d', $staffStart);
                    } else {
                        $staffStart = null;
                    }
                    if ($gender == "nam") {
                        $gender = \App\Defines\Staff::GENDER_MALE;
                    } else {
                        $gender = \App\Defines\Staff::GENDER_FEMALE;
                    }
                    if ($idNo == "" || $idIssueOfDate == "" || $idIssueOfPlace == "") {
                        throw new \Exception("Kiểm tra lại CMND/CCCD tại dòng số " . ($d[0] ?? "") . "");
                    }
                    if (strtotime(str_replace('/', '-', $idIssueOfDate)) == false) {
                        throw new \Exception("Kiểm tra lại Ngày cấp CCCD tại dòng số " . ($d[0] ?? "") . "");
                    }
                    $idIssueOfDate = Carbon::createFromFormat('d/m/Y', $idIssueOfDate)->format('Y-m-d');
                    if ($maritalStatus == "doc than") {
                        $maritalStatus = \App\Defines\Staff::MARITAL_STATUS_SINGLE;
                    } else {
                        $maritalStatus = \App\Defines\Staff::MARITAL_STATUS_MARRIED;
                    }
                    if ($qualification == "dai hoc") {
                        $qualification = \App\Defines\Staff::QUALIFICATION_UNIVERSITY;
                    } elseif ($qualification == "cao dang") {
                        $qualification = \App\Defines\Staff::QUALIFICATION_COLLEGE;
                    } elseif ($qualification == "trung cap") {
                        $qualification = \App\Defines\Staff::QUALIFICATION_INTERMEDIATE;
                    } elseif ($qualification == "sau dai hoc") {
                        $qualification = \App\Defines\Staff::QUALIFICATION_POST_GRADUATE;
                    } elseif ($qualification == "tot nghiep thpt") {
                        $qualification = \App\Defines\Staff::QUALIFICATION_HIGHSCHOOL;
                    } elseif ($qualification == "tot nghiep thcs") {
                        $qualification = \App\Defines\Staff::QUALIFICATION_SECONDARY;
                    } else {
                        $qualification = null;
                    }
                    if ($emergencyContact || $emergencyPhone) {
                        if ($emergencyContact == "" || $emergencyPhone == "") {
                            throw new \Exception("Thông tin Liên hệ khẩn cấp tại dòng số " . ($d[0] ?? "") . " cần nhập đủ");
                        }
                        if (!preg_match('/^0[0-9]{9}$/', $emergencyPhone)) {
                            throw new \Exception("Kiểm tra lại Điện thoại khẩn cấp tại dòng số " . ($d[0] ?? "") . "");
                        }
                    }
                    if (!isset($banks[$bankName])) {
                        throw new \Exception("Ngân hàng tại dòng số " . ($d[0] ?? "") . " chưa hỗ trợ");
                    }
                    if ($dlNo || $dlExpire || $dlClass) {
                        if ($dlNo == "" || $dlExpire == "" || $dlClass == "") {
                            throw new \Exception("Thông tin bằng lái xe tại dòng số " . ($d[0] ?? "") . " cần nhập đủ");
                        }
                        if (strtotime(str_replace('/', '-', $dlExpire)) == false) {
                            throw new \Exception("Kiểm tra lại Thời hạn bằng tại dòng số " . ($d[0] ?? "") . "");
                        }
                        $dlExpire = Carbon::createFromFormat('d/m/Y', $dlExpire)->format('Y-m-d');
                        $dlClasses = \App\Defines\Staff::getDriverLicensesForOption();
                        if (!isset($dlClasses[$dlClass])) {
                            throw new \Exception("Kiểm tra lại Hạng bằng tại dòng số " . ($d[0] ?? "") . "");
                        }
                    }
                    $haveData = false;
                    $families = [];
                    $j = 1;
                    for ($i = 27; $i < count($d); $i = $i + 8) {
                        $fFullname = trim($d[$i]);
                        if ($fFullname == "") break;
                        $fTaxCode = trim($d[$i + 1]);
                        $fRelationship = trim($d[$i + 2]);
                        $fDob = trim($d[$i + 3]);
                        $fGender = trim($d[$i + 4]);
                        $fDepent = trim($d[$i + 5]);
                        $fDepentDate = '01/' . trim($d[$i + 6]);
                        $tDepentDate = trim($d[$i + 7]);
                        $tmpRelationship = strtolower(\App\Helper\HString::removeVietnameseSign($fRelationship));
                        $tmpDependemt = strtolower(\App\Helper\HString::removeVietnameseSign($fDepent));
                        $fGender = strtolower(\App\Helper\HString::removeVietnameseSign($fGender));
                        $fRelationship = "";
                        $fDepent = 0;
                        if ($tmpDependemt == 'co') $fDepent = 1;
                        switch ($tmpRelationship) {
                            case 'vo':
                                $fRelationship = \App\Defines\Staff::FAMILY_RELATIONSHIP_WIFE;
                                break;
                            case 'chong':
                                $fRelationship = \App\Defines\Staff::FAMILY_RELATIONSHIP_HUSBAND;
                                break;
                            case 'con':
                                $fRelationship = \App\Defines\Staff::FAMILY_RELATIONSHIP_CHILDREN;
                                break;
                            case 'cha':
                                $fRelationship = \App\Defines\Staff::FAMILY_RELATIONSHIP_FATHER;
                                break;
                            case 'me':
                                $fRelationship = \App\Defines\Staff::FAMILY_RELATIONSHIP_MOTHER;
                                break;
                            case 'anh':
                                $fRelationship = \App\Defines\Staff::FAMILY_RELATIONSHIP_BROTHER;
                                break;
                            case 'chi':
                                $fRelationship = \App\Defines\Staff::FAMILY_RELATIONSHIP_SISTER;
                                break;
                            case 'em':
                                $fRelationship = \App\Defines\Staff::FAMILY_RELATIONSHIP_YOUNGER;
                                break;
                        }
                        if ($fDepent) {
                            if (!$fDepentDate || $fDepentDate &&  strtotime(str_replace('/', '-', $fDepentDate)) == false) {
                                throw new \Exception("Kiểm tra lại `Phụ thuộc từ` của người Phụ thuộc số {$j} tại dòng số " . ($d[0] ?? "") . "");
                            }
                            if ($tDepentDate) {
                                $tDepentDate = '01/' . $tDepentDate;
                                if (strtotime(str_replace('/', '-', $tDepentDate)) == false) {
                                    throw new \Exception("Kiểm tra lại `Phụ thuộc đến` của người Phụ thuộc số {$j} tại dòng số " . ($d[0] ?? "") . "");
                                }
                            }
                        } else {
                            $fDepentDate = null;
                            $tDepentDate = null;
                        }
                        if ($fRelationship == "") {
                            throw new \Exception("Kiểm tra lại Mối quan hệ của người Phụ thuộc số {$j} tại dòng " . ($d[0] ?? "") . "");
                        }
                        if (!$fDob || $fDob &&  strtotime(str_replace('/', '-', $fDob)) == false) {
                            throw new \Exception("Kiểm tra lại Ngày sinh của người Phụ thuộc số {$j} tại dòng số " . ($d[0] ?? "") . "");
                        }
                        if ($fGender == "nam") {
                            $fGender = \App\Defines\Staff::GENDER_MALE;
                        } else {
                            $fGender = \App\Defines\Staff::GENDER_FEMALE;
                        }
                        array_push($families, [
                            'fullname'  => $fFullname,
                            'tax_code'  => $fTaxCode,
                            'dob'       => $fDob ? Carbon::createFromFormat('d/m/Y', $fDob)->format('Y-m-d') : null,
                            'gender'    => $fGender,
                            'dependent' => $fDepent,
                            'dependent_from' => $fDepentDate ? Carbon::createFromFormat('d/m/Y', $fDepentDate)->format('Y-m-d') : null,
                            'dependent_to'  => $tDepentDate ? Carbon::createFromFormat('d/m/Y', $tDepentDate)->format('Y-m-d') : null,
                            'relationship'  => $fRelationship,
                        ]);
                        $j++;
                    }
                    array_push($staffData, [
                        'code'      => $code,
                        'fullname'  => $fullname,
                        'email'     => $email,
                        'addresses' => trim($d[4]),
                        'nationality'   => trim($d[6]),
                        'date_of_birth' => $dob,
                        'phone'     => $phone,
                        'gender'    => $gender,
                        'id_card_no' => $idNo,
                        'issued_on' => $idIssueOfDate,
                        'issued_at' => $idIssueOfPlace,
                        'activated' => 1,
                        'active'    => $request->active,
                        'code_timekeeping' => $codeTimekeeping,
                        // 'password'      => bcrypt($request->password),
                        'created_by'    => auth()->id(),
                        // 'created_at'    => Carbon::now()->format('Y-m-d H:i:s'),
                        'tax_code'      => trim($d[18]),
                        'insurance_no'  => trim($d[19]),
                        'bank_name'     => $bankName,
                        'bank_account'  => trim($d[20]),
                        'driver_license_no'     => $dlNo,
                        'driver_license_class'  => $dlClass,
                        'driver_license_expire' => $dlExpire ? $dlExpire : null,
                        'families'  => $families,
                        'ethnicity'     => trim($d[14]),
                        'marital_status'    => $maritalStatus,
                        'qualification'     => $qualification,
                        'emergency_contact' => $emergencyContact,
                        'emergency_phone'   => $emergencyPhone,
                        'staff_start'       => $staffStart,
                        'domicile'          => $dop,
                    ]);
                }
                foreach ($staffData as $sData) {
                    $families = $sData['families'];
                    unset($sData['families']);
                    $user = User::create($sData);
                    foreach ($families as $family) {
                        $family['staff_id'] = $user->id;
                        StaffFamily::create($family);
                    }
                }
                $response['message'] = trans('system.success');
                Session::flash('message', $response['message']);
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

    public function download(Request $request)
    {
        $file = public_path() . "/assets/media/files/templates/staffs-01Jul.xlsx";
        $headers = [
            'Content-Type: application/xls',
        ];
        return response()->download($file, 'template-staffs-' . time() . '.xlsx', $headers);
    }

    public function roles(Request $request, $id)
    {
        $teams = Team::pluck('name', 'id')->toArray();
        $user = User::find(intval($id));
        if (is_null($user)) {
            Session::flash('message', trans('system.have_an_error'));
            Session::flash('alert-class', 'danger');
            return redirect()->route('admin.staffs.index');
        }
        $firstRole = $user->roles()->first();
        $pGroups = Permission::pluck('module', 'module')->toArray();
        foreach ($pGroups as $key => $value) {
            $tmp = Permission::where('module', $key)->orderBy('action')->select('id', 'display_name', 'action')->get()->toArray();
            $pGroups[$key] = array_column($tmp, 'id', 'action');
        }
        $morePermissions = DB::table('permission_user')->where('user_id', $id)->pluck('permission_id', 'permission_id')->toArray();
        $companies = Company::where('status', 1)->select('shortened_name', 'id')->get()->keyBy('id')->toArray();
        foreach ($companies as $company) {
            $departments = \DB::table('departments')->where('departments.status', 1)->where('company_id', $company['id'])->join('companies', 'companies.id', '=', 'departments.company_id')->selectRaw("departments.name, departments.id")->pluck('name', 'id')->toArray(); //CONCAT(companies.shortened_name, ' - ', departments.name) as
            $companies[$company['id']]['departments'] = $departments;
        }
        $roles = $user->roles()->get();
        $permissionByRole = [];
        foreach ($roles as $role) {
            $permissions = $role->permissions()->pluck("id")->toArray();
            foreach ($permissions as $permission) {
                $permissionByRole[$permission] = $permission;
            }
        }

        // dd($firstRole);
        return view('backend.staffs.roles', compact('user', 'pGroups', 'permissions', 'firstRole', 'teams', 'companies', 'permissionByRole', 'morePermissions'));
    }

    public function storeRoles(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => ""];
        $statusCode = 200;
        if ($request->ajax()) {
            try {
                $user = User::find(intval($request->input('id')));
                $permissions = $request->input('permissions');
                $currentPermissions = DB::table('permission_user')->where("user_id", intval($request->input('id')))->get()->keyBy('permission_id');
                foreach ($permissions as $newPermissionId) {
                    if (isset($currentPermissions[$newPermissionId])) {
                        unset($currentPermissions[$newPermissionId]);
                    } else {
                        $permission = Permission::find(intval($newPermissionId));
                        if (is_null($permission)) continue;
                        $permissionsInModule = DB::table('permissions')->where('module', $permission->module)->pluck('id', 'id')->toArray();
                        // tim cung module de add permission object
                        $permissionUser = PermissionUser::where('user_id', $user->id)->whereIn('permission_id', $permissionsInModule)->first();
                        $permissionUserObjects = collect([]);
                        if (!is_null($permissionUser)) $permissionUserObjects = PermissionUserObject::where('permission_user_id', $permissionUser->id)->get();
                        $permissionUser = PermissionUser::create([
                            'permission_id' => $newPermissionId,
                            'user_id'       => $user->id,
                            'user_type'     => User::class,
                            'manager_other' => (!is_null($permissionUser) && $permissionUser->manager_other) ? 1 : 0,
                        ]);
                        // $newPermission = $user->attachPermission($newPermissionId);
                        foreach ($permissionUserObjects as $permissionUserObject) {
                            PermissionUserObject::create([
                                'permission_user_id' => $permissionUser->id,
                                'object_id'         => $permissionUserObject->object_id,
                                'object_type'       => $permissionUserObject->object_type,
                            ]);
                        }
                    }
                }
                foreach ($currentPermissions as $delId => $delPermission) {
                    DB::table("permission_user_objects")->where("permission_user_id", $delPermission->id)->delete();
                    DB::table("permission_user")->where("id", $delPermission->id)->delete();
                }
                $response['message'] = trans('system.success');
                $response['data'] = $user;
            } catch (\Exception $e) {
                if ($statusCode == 200) $statusCode = 500;
                $response['message'] = $e->getLine();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function getMoreRoles(Request $request)
    {
        $response = ['message' => trans('system.have_an_error'), 'data' => []];
        $statusCode = 400;
        if ($request->ajax()) {
            try {
                $user = User::find(intval($request->user_id));
                if (is_null($user)) throw new \Exception($response['message'], 1);
                $_permissions = Permission::where('module', $request->module_id)->pluck('id', 'id')->toArray();
                if (count($_permissions) == 0) throw new \Exception("Không tìm thấy Module", 1);
                $curPermissions = PermissionUser::where('user_id', $user->id)->whereIn('permission_id', $_permissions)->get();
                if ($curPermissions->count() == 0) {
                    $continue = 0;
                    $roles = $user->roles()->get();
                    foreach ($roles as $role) {
                        $permissions = $role->permissions()->pluck("id")->toArray();
                        foreach ($permissions as $permission) {
                            if (isset($_permissions[$permission])) {
                                $continue = 1;
                                break;
                            }
                        }
                    }
                    if ($continue == 0) throw new \Exception("Vui lòng Thêm Quyền thao tác trên Module cho Nhân viên trước", 1);
                }
                $managerOther   = $curPermissions[0]->manager_other;
                $companies      = [];
                $departments    = [];
                $teams          = [];
                $curPermissions = array_column($curPermissions->toArray(), 'id');
                if ($managerOther) {
                    $companies = PermissionUserObject::where('object_type', Company::class)->where('permission_user_id', $curPermissions)->pluck('object_id')->toArray();
                    $departments = PermissionUserObject::where('object_type', Department::class)->where('permission_user_id', $curPermissions)->pluck('object_id')->toArray();
                    $teams = PermissionUserObject::where('object_type', Team::class)->where('permission_user_id', $curPermissions)->pluck('object_id')->toArray();
                }
                $response['message'] = trans('system.success');
                $response['data']['manager_other'] = $managerOther;
                $response['data']['companies'] = $companies;
                $response['data']['departments'] = $departments;
                $response['data']['teams'] = $teams;
                $statusCode = 200;
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }

    public function saveMoreRoles(Request $request)
    {
        $response = ['message' => trans('system.have_an_error')];
        $statusCode = 400;
        if ($request->ajax()) {
            try {
                $user = User::find(intval($request->user_id));
                if (is_null($user)) throw new \Exception($response['message'], 1);
                $managerOther   = intval($request->manager_other);
                $companies      = $request->companies;
                $departments    = $request->departments;
                $teams          = $request->teams;
                $_permissions = Permission::where('module', $request->module_id)->pluck('id', 'id')->toArray();
                if (count($_permissions) == 0) throw new \Exception("Không tìm thấy Module", 1);
                $tmp = $curPermissions = PermissionUser::where('user_id', $user->id)->whereIn('permission_id', $_permissions)->get();
                $curPermissions = array_column($curPermissions->toArray(), 'id', 'id');
                $permissionsFromRole = [];
                if (count($curPermissions) == 0) {
                    $continue = 0;
                    $roles = $user->roles()->get();
                    foreach ($roles as $role) {
                        $permissions = $role->permissions()->pluck("id")->toArray();
                        foreach ($permissions as $permission) {
                            if (isset($_permissions[$permission])) {
                                $continue = 1;
                                $permissionsFromRole[$permission] = $permission;
                            }
                        }
                    }
                    if ($continue == 0) throw new \Exception("Vui lòng Thêm Quyền thao tác trên Module cho Nhân viên trước", 1);
                    // add them vao bang
                }
                $objects = PermissionUserObject::whereIn('permission_user_id', $curPermissions)->get();
                if (!$managerOther) {
                    foreach ($objects as $object) {
                        $object->deleted_by = $request->user()->id;
                        $object->save();
                        $object->delete();
                    }
                } else {
                    if (count($curPermissions) == 0) {
                        foreach ($permissionsFromRole as $permissionFromRole) {
                            PermissionUser::create(['user_id' => $user->id, 'permission_id' => $permissionFromRole, 'user_type' => User::class]);
                        }
                        $tmp = $curPermissions = PermissionUser::where('user_id', $user->id)->whereIn('permission_id', $_permissions)->get();
                        $curPermissions = array_column($curPermissions->toArray(), 'id', 'id');
                    }
                    $_companies = $objects->where('object_type', Company::class)->keyBy('id');
                    if (is_array($companies)) {
                        foreach ($companies as $companyId) {
                            foreach ($curPermissions as $curPermissionId) {
                                $existedCompany = $_companies->where('object_id', $companyId)->where('permission_user_id', $curPermissionId)->first();
                                if (is_null($existedCompany)) {
                                    PermissionUserObject::create([
                                        'permission_user_id' => $curPermissionId,
                                        'object_id'         => $companyId,
                                        'object_type'       => Company::class,
                                    ]);
                                } else {
                                    $_companies->forget($existedCompany->id);
                                }
                            }
                        }
                    }
                    foreach ($_companies as $object) {
                        $object->deleted_by = $request->user()->id;
                        $object->save();
                        $object->delete();
                    }
                    $_departments = $objects->where('object_type', Department::class)->keyBy('id');
                    if (is_array($departments)) {
                        foreach ($departments as $departmentId) {
                            foreach ($curPermissions as $curPermissionId) {
                                $existedDepartment = $_departments->where('object_id', $departmentId)->where('permission_user_id', $curPermissionId)->first();
                                if (is_null($existedDepartment)) {
                                    PermissionUserObject::create([
                                        'permission_user_id' => $curPermissionId,
                                        'object_id'         => $departmentId,
                                        'object_type'       => Department::class,
                                    ]);
                                } else {
                                    $_departments->forget($existedDepartment->id);
                                }
                            }
                        }
                    }
                    foreach ($_departments as $object) {
                        $object->deleted_by = $request->user()->id;
                        $object->save();
                        $object->delete();
                    }
                    $_teams = $objects->where('object_type', Team::class)->keyBy('id');
                    if (is_array($teams)) {
                        foreach ($teams as $teamId) {
                            foreach ($curPermissions as $curPermissionId) {
                                $existedTeam = $_teams->where('object_id', $teamId)->where('permission_user_id', $curPermissionId)->first();
                                if (is_null($existedTeam)) {
                                    PermissionUserObject::create([
                                        'permission_user_id' => $curPermissionId,
                                        'object_id'         => $teamId,
                                        'object_type'       => Team::class,
                                    ]);
                                } else {
                                    $_teams->forget($existedTeam->id);
                                }
                            }
                        }
                    }
                    foreach ($_teams as $object) {
                        $object->deleted_by = $request->user()->id;
                        $object->save();
                        $object->delete();
                    }
                }
                foreach ($tmp as $t) {
                    $t->manager_other = $managerOther;
                    $t->save();
                }
                $statusCode = 200;
                $response['message'] = trans('system.success');
                Session::flash('message', trans('system.success'));
                Session::flash('alert-class', 'success');
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage() . $e->getLine();
            } finally {
                return response()->json($response, $statusCode);
            }
        } else {
            $statusCode = 405;
            return response()->json($response, $statusCode);
        }
    }
}
