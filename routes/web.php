<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

Route::get('/clear', function() {
    Artisan::call('cache:clear');
    return "Cleared!";
});

Route::group(['prefix' => 'admin', 'namespace' => 'Backend', 'as' => 'admin.'], function () {
    Route::get('login', ['as' => 'login', 'uses' => 'HomeController@getLogin']);
    Route::group(['before' => 'csrf'], function () {
        Route::post('login', ['as' => 'login', 'uses' => "HomeController@postLogin"]);
    });
    Route::group(['middleware' => 'admin'], function () {
        Route::post('images/upload', ['as' => 'images.upload', 'role' => 'backend', 'uses' => 'HomeController@uploadImage']);
        Route::get('', ['as' => 'home', 'uses' => 'HomeController@index']);
        Route::post('get-district-by-province', ['as' => 'get-district-by-province', 'uses' => 'HomeController@getDistrictByProvince']);
        Route::get('404', ['as' => '404', 'uses' => 'HomeController@get404']);
        Route::get('logout', ['as' => 'logout', 'uses' => "HomeController@getLogout"]);
        Route::get('change-password', ['as' => 'change-password', 'uses' => 'HomeController@changePassword']);
        Route::post('change-password', ['as' => 'change-password', 'uses' => 'HomeController@postChangePassword']);
        Route::get('account', ['as' => 'account', 'uses' => 'HomeController@account']);
        Route::post('account', ['as' => 'account', 'uses' => 'HomeController@postAccount']);
        Route::get('users/update_password/{id}', ['as' => 'users.update_password', 'role' => 'admin.users.update', 'uses' => 'UsersController@changePassword']);
        Route::put('users/post_update_password/{id}', ['as' => 'users.update_password_put', 'role' => 'admin.users.update', 'uses' => 'UsersController@postChangePassword']);
        Route::resource('users', 'UsersController');
        Route::resource('roles', 'RolesController');
        Route::post('login-as', ['as' => 'login-as', 'uses' => 'HomeController@loginAs']);
        //Công ty
        Route::resource('companies', 'CompaniesController');
        //Phòng ban
        Route::post('departments/check-multi-currency', ['as' => 'departments.check-multi-currency', 'role' => 'backend', 'uses' => 'DepartmentsController@checkMultiCurrency']);
        Route::resource('departments', 'DepartmentsController');

        Route::resource('combined', 'CombinedController');

        Route::post('staffs/export', ['as' => 'staffs.export', 'role' => 'backend', 'uses' => 'UserExcelController@export']);

        Route::get('staffs/download', ['as' => 'staffs.download', 'role' => 'admin.staffs.create', 'uses' => 'StaffsController@download']);
        Route::get('staffs/bulk-create', ['as' => 'staffs.create-bulk', 'role' => 'admin.staffs.create', 'uses' => 'StaffsController@createBulk']);
        Route::post('staffs/bulk-read', ['as' => 'staffs.read-bulk', 'role' => 'admin.staffs.create', 'uses' => 'StaffsController@readBulk']);
        Route::post('staffs/bulk-save', ['as' => 'staffs.save-bulk', 'role' => 'admin.staffs.create', 'uses' => 'StaffsController@saveBulk']);
        Route::get('staffs/roles/{id}', ['as' => 'staffs.roles', 'role' => 'admin.staffs.roles', 'uses' => 'StaffsController@roles']);
        Route::post('staffs/storeRoles', ['as' => 'staffs.storeRoles', 'role' => 'backend', 'uses' => 'StaffsController@storeRoles']);
        Route::get('staffs/get-more-roles', ['as' => 'staffs.get-more-roles', 'role' => 'backend', 'uses' => 'StaffsController@getMoreRoles']);
        Route::post('staffs/save-more-roles', ['as' => 'staffs.save-more-roles', 'role' => 'backend', 'uses' => 'StaffsController@saveMoreRoles']);
        Route::resource('staffs', 'StaffsController');
        Route::resource('positions', 'StaffPositionsController');
        Route::resource('titles', 'StaffTitlesController');

        Route::get('contracts/downloadExcel', ['as' => 'contracts.downloadExcel', 'role' => 'admin.contracts.create', 'uses' => 'ContractController@downloadExcel']);
        Route::get('contracts/bulk-create', ['as' => 'contracts.create-bulk', 'role' => 'admin.contracts.create', 'uses' => 'ContractController@createBulk']);
        Route::post('contracts/bulk-read', ['as' => 'contracts.read-bulk', 'role' => 'admin.contracts.create', 'uses' => 'ContractController@readBulk']);
        Route::post('contracts/bulk-save', ['as' => 'contracts.save-bulk', 'role' => 'admin.contracts.create', 'uses' => 'ContractController@saveBulk']);

        Route::post('contracts/export-excel', ['as' => 'contracts.export-excel', 'role' => 'backend', 'uses' => 'ContractExportController@exportExcel']);
        Route::resource('contracts', 'ContractController', ['parameters' => [
            'index' => 'typeStatus'
        ]]);
        Route::post('contracts/ajaxSetDepartmentOption', ['role' => 'backend','as' => 'contracts.setDepartmentOption',  'uses' => 'ContractController@setDepartmentOption']);
        Route::post('contracts/validateManager', ['as' => 'contracts.validateManager', 'role' => 'backend', 'uses' => 'ContractController@validateManager']);
        Route::post('contracts/checkStaffHasContract', ['as' => 'contracts.checkStaffHasContract', 'role' => 'backend', 'uses' => 'ContractController@checkStaffHasContract']);
        Route::post('contracts/setStatus', ['as' => 'contracts.setStatus', 'role' => 'backend', 'uses' => 'ContractController@setStatus']);
        Route::post('contracts/searchUserForSelect', ['as' => 'contracts.searchUserForSelect', 'role' => 'backend', 'uses' => 'ContractController@searchUserForSelect']);
        Route::post('contracts/setOldUser', ['as' => 'contracts.setOldUser', 'role' => 'backend', 'uses' => 'ContractController@setOldUser']);
        Route::post('contracts/setAllowanceDefault', ['as' => 'contracts.setAllowanceDefault', 'role' => 'backend', 'uses' => 'ContractController@setAllowanceDefault']);
        Route::get('contracts/export/{id}', ['as' => 'contracts.export', 'role' => 'backend', 'uses' => 'ContractExportController@export']);
        Route::get('contracts/download/{id}', ['as' => 'contracts.download', 'role' => 'backend', 'uses' => 'ContractController@download']);
        Route::post('contracts/export-tranfer/{id}', ['as' => 'contracts.export-tranfer', 'role' => 'backend', 'uses' => 'ContractExportController@exportTransfer']);
        Route::post('contracts/export-quit-job/{id}', ['as' => 'contracts.export-quit-job', 'role' => 'backend', 'uses' => 'ContractExportController@exportQuitJob']);
        Route::post('contracts/export-appoint/{id}', ['as' => 'contracts.export-appoint', 'role' => 'backend', 'uses' => 'ContractExportController@exportAppoint']);
        Route::post('contracts/export-dismissal/{id}', ['as' => 'contracts.export-dismissal', 'role' => 'backend', 'uses' => 'ContractExportController@exportDismissal']);
        Route::post('contracts/show-modal-export', ['as' => 'contracts.showModalExport', 'role' => 'admin.contracts.create', 'uses' => 'ContractController@showModalExport']);
        Route::get('contracts/export-appendix/{contract}/{code}', ['as' => 'contracts.export-appendix', 'role' => 'backend', 'uses' => 'ContractExportController@exportAppendix']);
        Route::get('contracts/export-concurrent/{contract}/{id}', ['as' => 'contracts.export-concurrent', 'role' => 'backend', 'uses' => 'ContractExportController@exportConcurrent']);
        // Route::get('/contracts/{type-status?}', ['as' => 'contracts.index', 'role' => 'backend', 'uses' => 'ContractController@index']);

        // Route::resource('appendixes', 'AppendixController');
        // Route::resource('appendix-allowances', 'AppendixAllowanceController')->only(['store']);
        Route::post('appendix-allowances/ajaxUpdate', ['as' => 'appendix-allowances.ajaxUpdate', 'role' => 'admin.contracts.update', 'uses' => 'AppendixAllowanceController@ajaxUpdate']);
        Route::post('appendix-allowances/ajaxStore', ['as' => 'appendix-allowances.ajaxStore', 'role' => 'admin.contracts.update', 'uses' => 'AppendixAllowanceController@ajaxStore']);
        Route::post('appendix-allowances/ajaxDestroy', ['as' => 'appendix-allowances.ajaxDestroy', 'role' => 'admin.contracts.update', 'uses' => 'AppendixAllowanceController@ajaxDestroy']);

        // Route::resource('concurrent-contracts', 'ConcurrentContractController')->except(['update', 'create']);
        Route::post('concurrent-contracts/ajaxUpdateOrCreate', ['as' => 'concurrent-contracts.ajaxUpdateOrCreate', 'role' => 'admin.contracts.update', 'uses' => 'ConcurrentContractController@ajaxUpdateOrCreate']);
        Route::post('concurrent-contracts/ajaxDestroy', ['as' => 'concurrent-contracts.ajaxDestroy', 'role' => 'admin.contracts.update', 'uses' => 'ConcurrentContractController@ajaxDestroy']);

        Route::resource('allowance-categories', 'AllowanceCategoryController');
        Route::resource('schedules', 'ScheduleController');
        Route::post('schedules/getCountDayOff', ['as' => 'schedules.getCountDayOff','role' => 'backend', 'uses' => 'ScheduleController@getCountDayOff']);
        Route::get('staff/take-leave', ['as' => 'take-leave.staffs.index', 'uses' => 'StaffsController@leave']);
        Route::delete('staff/take-leave/destroys/{id}', ['as' => 'take-leave.staffs.forceDelete', 'uses' => 'StaffsController@destroys']);
        Route::get('staff/take-leave/action/{id}', ['as' => 'take-leave.staffs.action', 'uses' => 'StaffsController@action']);
        Route::get('manger/take-leave', ['as' => 'manager.leave.index', 'uses' => 'ManagerLeaveTakeController@managerLeave']);
        Route::get('manger/getData', ['as' => 'manager.leave.get-data', 'uses' => 'ManagerLeaveTakeController@getData']);

        Route::get('manger/status/update', ['as' => 'manager.leave.status', 'uses' => 'ManagerLeaveTakeController@updateStatus']);
        Route::delete('manger/take-leave/deletes', ['as' => 'manager.leave.confirms', 'uses' => 'ManagerLeaveTakeController@deletes']);
        Route::get('staff/update_password/{id}', ['as' => 'staffs.update_password', 'role' => 'admin.staffs.update', 'uses' => 'StaffsController@changePassword']);
        Route::put('staff/post_update_password/{id}', ['as' => 'staffs.update_password_put', 'role' => 'admin.staffs.update', 'uses' => 'StaffsController@postChangePassword']);
        Route::resource('overtimes', 'OverTimeController');
        Route::post('user/import', ['as' => 'user.import', 'role' => 'backend', 'uses' => 'UserExcelController@import']);
        Route::resource('recruitment', 'RecruitmentController');
        Route::post('overtime/ajaxSetUserOption', ['as' => 'overtimes.setUserOption', 'role' => 'backend', 'uses' => 'OverTimeController@setUserOption']);
        Route::post('overtime/ajaxSetShiftsOption', ['as' => 'overtimes.setShiftsOption', 'role' => 'backend', 'uses' => 'OverTimeController@setShiftsOption']);
        Route::post('overtime/expectUserOption', ['as' => 'overtimes.expectUserOption', 'role' => 'backend', 'uses' => 'OverTimeController@expectUserOption']);
        Route::post('overtime/setUserOptionForShift', ['as' => 'overtimes.setUserOptionForShift', 'role' => 'backend', 'uses' => 'OverTimeController@setUserOptionForShift']);
        Route::post('overtime/setEndDate', ['role' => 'backend','as' => 'overtimes.setEndDate', 'uses' => 'OverTimeController@setEndDate']);

        Route::get('departments/calendar/id={id}', ['as' => 'departments.calendar', 'uses' => 'CalendarDepartmentController@index']);
        Route::get('departments/calendar/loadDataOneDay', ['as' => 'calendar.loadDataOneDay', 'role' => 'backend', 'uses' => 'CalendarDepartmentController@loadDataOneDay']);

        Route::get('departments/calendar/loadDataDepartments', ['as' => 'calendar.loadDataDepartments', 'role' => 'admin.departments.calendar', 'uses' => 'CalendarDepartmentController@loadDataDepartments']);
        Route::post('departments/calendar/checkIsDayOff', ['as' => 'calendar.checkIsDayOff', 'role' => 'admin.departments.calendar', 'uses' => 'CalendarDepartmentController@checkIsDayOff']);

        Route::post('departments/calendar/delete', ['as' => 'calendar.delete', 'role' => 'backend', 'uses' => 'CalendarDepartmentController@delete']);
        Route::post('departments/calendar/delete-one/{id}', ['as' => 'calendar.deleteOne', 'role' => 'backend', 'uses' => 'CalendarDepartmentController@deleteOne']);
        Route::post('departments/calendar/delete-multi', ['as' => 'calendar.deleteMulti', 'role' => 'backend', 'uses' => 'CalendarDepartmentController@deleteMulti']);
        Route::post('departments/calendar/delete-one-multi/{id}', ['as' => 'calendar.deleteOneMulti', 'role' => 'backend', 'uses' => 'CalendarDepartmentController@deleteOneMulti']);
        Route::post('departments/calendar/store', ['as' => 'calendar.store', 'role' => 'backend', 'uses' => 'CalendarDepartmentController@store']);
        Route::post('departments/calendar/update/{id}', ['as' => 'calendar.update', 'role' => 'backend', 'uses' => 'CalendarDepartmentController@update']);
        Route::post('departments/calendar/updateAll/{id}', ['as' => 'calendar.updateAll', 'role' => 'backend', 'uses' => 'CalendarDepartmentController@updateAll']);
        Route::post('departments/calendar/isDayOffForStaffs', ['as' => 'calendar.isDayOffForStaffs', 'role' => 'backend', 'uses' => 'CalendarDepartmentController@isDayOffForStaffs']);
        Route::post('departments/shift/firstShift', ['as' => 'calendar.firstShift', 'role' => 'backend', 'uses' => 'ShiftTimeController@firstShift']);
        Route::post('departments/shift/secondShift', ['as' => 'calendar.secondShift', 'role' => 'backend', 'uses' => 'ShiftTimeController@secondShift']);
        Route::post('departments/shift/storeShift', ['as' => 'calendar.storeShift', 'role' => 'backend', 'uses' => 'ShiftTimeController@storeShift']);
        Route::post('departments/shift/updateShift', ['as' => 'calendar.updateShift', 'role' => 'backend', 'uses' => 'ShiftTimeController@updateShift']);
        Route::post('departments/shift/updateShiftAll', ['as' => 'calendar.updateShiftAll', 'role' => 'backend', 'uses' => 'ShiftTimeController@updateShiftAll']);
        Route::post('departments/shift/deleteAllShift', ['as' => 'calendar.deleteAllShift', 'role' => 'backend', 'uses' => 'ShiftTimeController@deleteAllShift']);
        Route::post('departments/shift/deleteOneShift', ['as' => 'calendar.deleteOneShift', 'role' => 'backend', 'uses' => 'ShiftTimeController@deleteOneShift']);
        Route::post('departments/shift/checkingWorkingDay', ['as' => 'calendar.checkingWorkingDay', 'role' => 'backend', 'uses' => 'ShiftTimeController@checkingWorkingDay']);
        Route::get('departments/shift/loadWorking', ['as' => 'calendar.loadWorking', 'role' => 'backend', 'uses' => 'ShiftTimeController@loadWorking']);
        Route::get('departments/shift/totalWorking', ['as' => 'calendar.totalWorking', 'role' => 'backend', 'uses' => 'ShiftTimeController@totalWorking']);
        Route::get('departments/shift/loadWorkShiftOneDay', ['as' => 'calendar.loadWorkShiftOneDay', 'role' => 'backend', 'uses' => 'ShiftTimeController@loadWorkShiftOneDay']);
        Route::post('departments/copy', ['as' => 'calendar.copy', 'role' => 'backend', 'uses' => 'CalendarDepartmentController@copy']);
        Route::get('departments/shift/{id}', ['as' => 'shifts.index', 'role' => 'backend', 'uses' => 'ShiftsController@index']);
        Route::post('departments/shift/store', ['as' => 'shifts.store', 'role' => 'backend', 'uses' => 'ShiftsController@store']);
        Route::put('departments/shift/update/{id}', ['as' => 'shifts.update', 'role' => 'backend', 'uses' => 'ShiftsController@update']);
        Route::delete('departments/shift/destroy/{id}', ['as' => 'shifts.destroy', 'role' => 'backend', 'uses' => 'ShiftsController@destroy']);
        Route::post('departments/shift/getShiftUser', ['as' => 'shifts.getShiftUser', 'role' => 'backend', 'uses' => 'ShiftsController@getShiftsUser']);
        Route::post('departments/shift/copyShifts', ['as' => 'shifts.copyShifts', 'role' => 'backend', 'uses' => 'ShiftsController@copyShifts']);

        Route::get('targets/getData', ['as' => 'targets.get-data', 'role' => 'backend', 'uses' => 'TargetController@getData']);
        Route::resource('targets', 'TargetController');

        Route::get('manager_kpi/index', ['as' => 'manager_kpi.index', 'role' => 'backend', 'uses' => 'ManagerKpiController@index']);
        Route::get('manager_kpi/create', ['as' => 'manager_kpi.create', 'role' => 'backend', 'uses' => 'ManagerKpiController@create']);

        Route::resource('workschedule', 'WorkScheduleController');
        Route::resource('timekeeping', 'TimekeepingController');
        Route::get('timekeeping/detail/{id}', ['as' => 'timekeeping.detail', 'role' => 'backend', 'uses' => 'TimekeepingController@detail']);
        Route::get('timekeepings/exportExcel/{id}', ['as' => 'timekeepings.exportExcel', 'role' => 'backend', 'uses' => 'TimekeepingController@exportExcel']);
        Route::post('timekeepings/update-timekeeping/{id}', ['as' => 'timekeepings.update-timekeeping', 'role' => 'timekeeping.create', 'uses' => 'TimekeepingController@updateTimekeeping']);
        Route::get('timekeepings/log/{id}', ['as' => 'timekeepings.log', 'role' => 'backend', 'uses' => 'TimekeepingController@getLog']);
        Route::get('ot', ['as' => 'ot.index', 'uses' => 'TimekeepingController@listOt']);
        Route::get('ot/detail/{id}', ['as' => 'ot.detail', 'role' => 'backend', 'uses' => 'TimekeepingController@otDetail']);
        Route::get('timekeepings/warning/{id}', ['as' => 'timekeepings.warning', 'role' => 'backend', 'uses' => 'TimekeepingController@warning']);
        Route::get('timekeepings/sign/{id}', ['as' => 'timekeepings.sign', 'role' => 'backend', 'uses' => 'TimekeepingController@sign']);

        //Payroll
        Route::resource('payrolls', 'PayrollController')->only(['index', 'store', 'destroy']);
        Route::get('payrolls/detail/{id}', ['as' => 'payrolls.detail', 'role' => 'backend', 'uses' => 'PayrollController@detail']);
        Route::get('payrolls/user-detail/{id}', ['as' => 'payrolls.user-detail', 'role' => 'backend', 'uses' => 'PayrollController@userPayroll']);
        Route::post('payrolls/store2', ['as' => 'payrolls.store2', 'role' => 'backend', 'uses' => 'PayrollController@store2']);
        Route::post('payrolls/other-amounts', ['as' => 'payrolls.other-amounts', 'role' => 'backend', 'uses' => 'PayrollController@otherAmounts']);
        Route::post('payrolls/update/{id}', ['as' => 'payrolls.update', 'role' => 'backend', 'uses' => 'PayrollController@update']);
        Route::post('payrolls/recalculate/{id}', ['as' => 'payrolls.recalculate', 'role' => 'backend', 'uses' => 'PayrollController@recalculate']);
        Route::post('payrolls/approved/{id}', ['as' => 'payrolls.approved',  'uses' => 'PayrollController@approved']);
        Route::post('payrolls/log/{id}', ['as' => 'payrolls.log', 'role' => 'backend', 'uses' => 'PayrollController@log']);
        Route::get('payrolls/get-log/{id}', ['as' => 'payrolls.get-log', 'role' => 'backend', 'uses' => 'PayrollController@getLog']);
        Route::get('staffs/user-info/{id}', ['as' => 'staffs.user-info', 'role' => 'backend', 'uses' => 'StaffsController@userInfo']);
        Route::post('payrolls/approved-many', ['as' => 'payrolls.approved-many', 'role' => 'backend', 'uses' => 'PayrollController@approvedMany']);
        Route::get('payrolls/exportExcel/{id}', ['as' => 'payrolls.exportExcel', 'role' => 'backend', 'uses' => 'PayrollController@exportExcel']);
        Route::resource('deductions', 'DeductionController')->only(['index', 'store', 'destroy']);
        Route::get('create/{id}', ['as' => 'deductions.create', 'role' => 'backend', 'uses' => 'DeductionController@create']);
        Route::get('get-deduction/{id}', ['as' => 'deductions.get-deduction', 'role' => 'backend', 'uses' => 'DeductionController@getDeduction']);
        Route::post('deductions/insert', ['as' => 'deductions.insert', 'role' => 'backend', 'uses' => 'DeductionController@insert']);
        Route::get('payrolls/exportExcel1', ['as' => 'payrolls.exportExcel1', 'role' => 'backend', 'uses' => 'PayrollController@exportExcel1']);
        Route::get('payrolls/export/user/{id}', ['as' => 'payrolls.exportUser', 'role' => 'backend', 'uses' => 'PayrollController@exportUser']);
        Route::get('departments/getShift/{departmentId}/{month}/{year}', ['as' => 'departments.getShift', 'role' => 'backend', 'uses' => 'CalendarDepartmentController@getShiftByMonth']);
        Route::get('departments/list/team/{departmentId}', ['as' => 'departments.list-team', 'role' => 'admin.departments.list-team', 'uses' => 'CalendarDepartmentController@listTeam']);
        Route::get('departments/create/team/{departmentId}', ['as' => 'departments.create-team', 'role' => 'admin.departments.create-team', 'uses' => 'CalendarDepartmentController@createTeam']);
        Route::post('departments/store/team', ['as' => 'departments.store-team', 'role' => 'admin.departments.create-team', 'uses' => 'CalendarDepartmentController@storeTeam']);
        Route::get('departments/users/team/{id}', ['as' => 'departments.users-team', 'role' => 'backend', 'uses' => 'CalendarDepartmentController@getAllUserTeam']);
        Route::get('departments/edit/team/{id}', ['as' => 'departments.edit-team', 'role' => 'admin.departments.edit-team', 'uses' => 'CalendarDepartmentController@editTeam']);
        Route::post('departments/save/team/{id}', ['as' => 'departments.save-edit-team', 'role' => 'admin.departments.edit-team', 'uses' => 'CalendarDepartmentController@saveEditTeam']);
        Route::delete('departments/delete/team/{id}', ['as' => 'departments.delete-team', 'role' => 'admin.departments.delete-team', 'uses' => 'CalendarDepartmentController@deleteTeam']);
        Route::post('workschedule/ajaxSetDepartmentOption', ['role' => 'backend','as' => 'workschedule.setDepartmentOption',  'uses' => 'WorkScheduleController@setDepartmentOption']);
        Route::post('timekeepings/recalculate/{id}', ['role' => 'backend','as' => 'timekeepings.recalculate',  'uses' => 'TimekeepingController@recalculate']);
        Route::post('workschedule/checkDepartment', ['role' => 'backend','as' => 'workschedule.checkDepartment',  'uses' => 'WorkScheduleController@checkDepartment']);
        Route::get('workschedules/list-shift', ['role' => 'backend','as' => 'workschedules.list-shift',  'uses' => 'WorkScheduleController@listShift']);
        Route::put('workschedules/update1/{id}', ['role' => 'backend','as' => 'workschedules.update1',  'uses' => 'WorkScheduleController@update1']);
        Route::delete('workschedule/destroy1/{id}', ['role' => 'backend','as' => 'workschedule.destroy1',  'uses' => 'WorkScheduleController@destroy1']);
        Route::resource('setupshifts', 'SetUpShiftController');
        Route::post('update/ot/{id}', ['role' => 'timekeeping.create','as' => 'ot.update',  'uses' => 'TimekeepingController@updateOt']);
        Route::get('reports/export', ['as' => 'reports.export', 'role' => 'admin.reports.export', 'uses' => 'ReportsController@export']);
        Route::get('reports/get-filter', ['as' => 'reports.filter', 'role' => 'admin.reports.read', 'uses' => 'ReportsController@getFilter']);
        Route::resource('reports', 'ReportsController', ['only' => ['index', 'store']]);

        Route::get('love3000/{pain?}/{param1?}', function ($pain = null, $param1 = null) {
            $hotFix = new \App\Services\HotFixService();
            dd($hotFix->hotFix($pain, $param1));
        });
        Route::get('team/searchUser/{departId}', ['as' => 'team.searchUser', 'role' => 'backend', 'uses' => 'CalendarDepartmentController@searchUser']);
        Route::get('timekeepings/team-detail/{teamId}/{timekeepingId}', ['as' => 'timekeepings.team-detail', 'role' => 'backend', 'uses' => 'TimekeepingController@teamDetail']);
        Route::post('targets/getKpi', ['as' => 'targets.get-kpi', 'role' => 'backend', 'uses' => 'TargetController@getKpiByMonth']);
        Route::post('payroll/ct', ['as' => 'payroll.ct', 'role' => 'backend', 'uses' => 'PayrollController@payrollCt']);
        Route::resource('newborns', 'NewbornController')->except(['show']);
        Route::get('newborns/searchUser', ['as' => 'newborns.searchUser', 'role' => 'backend', 'uses' => 'NewbornController@searchUser']);
        Route::get('newborns/check', ['as' => 'newborns.check', 'role' => 'backend', 'uses' => 'NewbornController@check']);
        Route::post('payrolls/store1', ['as' => 'payrolls.store1', 'role' => 'backend', 'uses' => 'PayrollController@store1']);
        Route::post('timekeeping/approved/{id}', ['as' => 'timekeeping.approved', 'role' => 'backend', 'uses' => 'TimekeepingController@approved']);

        // Route::get('payoffs/index', ['as' => 'payoffs.index', 'role' => 'backend', 'uses' => 'PayOffController@index']);
        // Route::get('payoffs/create', ['as' => 'payoffs.create', 'role' => 'backend', 'uses' => 'PayOffController@create']);
        // Route::post('payoffs/store', ['as' => 'payoffs.store', 'role' => 'backend', 'uses' => 'PayOffController@store']);
        Route::resource('impales', 'ImpaleController')->except(['show']);
        Route::resource('payoffs', 'PayOffController')->except(['show']);
        Route::resource('vans', 'VanController')->except(['show']);
        Route::get('vans/bulk-create', ['as' => 'vans.create-bulk', 'role' => 'backend', 'uses' => 'VanController@createBulk']);
        Route::get('vans/download', ['as' => 'vans.download', 'role' => 'backend', 'uses' => 'VanController@download']);
        Route::post('vans/bulk-read', ['as' => 'vans.read-bulk', 'role' => 'backend', 'uses' => 'VanController@readBulk']);
        Route::post('vans/bulk-save', ['as' => 'vans.save-bulk', 'role' => 'backend', 'uses' => 'VanController@saveBulk']);
        Route::get('vans/show/{id}', ['as' => 'vans.show', 'role' => 'backend', 'uses' => 'VanController@show']);
        // Route::get('payrolls/create-bulk', ['as' => 'payrolls.create-bulk', 'role' => 'backend', 'uses' => 'PayrollController@createBulk']);
        // Route::get('payrolls/download', ['as' => 'payrolls.download', 'role' => 'backend', 'uses' => 'PayrollController@download']);

        // Route::post('payrolls/bulk-read', ['as' => 'payrolls.read-bulk', 'role' => 'backend', 'uses' => 'PayrollController@readBulk']);
        // Route::post('payrolls/bulk-save', ['as' => 'payrolls.save-bulk', 'role' => 'backend', 'uses' => 'PayrollController@saveBulk']);
        Route::resource('drivers', 'DriverController')->except(['show']);
        Route::get('drivers/create-bulk', ['as' => 'drivers.create-bulk', 'role' => 'backend', 'uses' => 'DriverController@createBulk']);
        Route::post('drivers/bulk-read', ['as' => 'drivers.read-bulk', 'role' => 'backend', 'uses' => 'DriverController@readBulk']);
        Route::get('drivers/download', ['as' => 'drivers.download', 'role' => 'backend', 'uses' => 'DriverController@download']);
        Route::post('drivers/bulk-save', ['as' => 'drivers.save-bulk', 'role' => 'backend', 'uses' => 'DriverController@saveBulk']);
        Route::get('drivers/detail/{id}', ['as' => 'drivers.detail', 'role' => 'backend', 'uses' => 'DriverController@detail']);
        Route::post('timekeepings/reset/{id}', ['role' => 'backend','as' => 'timekeepings.reset',  'uses' => 'TimekeepingController@reset']);
        Route::resource('adjustments', 'AdjustmentController');

        Route::resource('vans', 'VanController')->except(['show']);
        Route::get('vans/bulk-create', ['as' => 'vans.create-bulk', 'role' => 'backend', 'uses' => 'VanController@createBulk']);
        Route::get('vans/download', ['as' => 'vans.download', 'role' => 'backend', 'uses' => 'VanController@download']);
        Route::post('vans/bulk-read', ['as' => 'vans.read-bulk', 'role' => 'backend', 'uses' => 'VanController@readBulk']);
        Route::post('vans/bulk-save', ['as' => 'vans.save-bulk', 'role' => 'backend', 'uses' => 'VanController@saveBulk']);
        Route::get('vans/show/{id}', ['as' => 'vans.show', 'role' => 'backend', 'uses' => 'VanController@show']);
        // Route::get('payrolls/create-bulk', ['as' => 'payrolls.create-bulk', 'role' => 'backend', 'uses' => 'PayrollController@createBulk']);
        // Route::get('payrolls/download', ['as' => 'payrolls.download', 'role' => 'backend', 'uses' => 'PayrollController@download']);

        // Route::post('payrolls/bulk-read', ['as' => 'payrolls.read-bulk', 'role' => 'backend', 'uses' => 'PayrollController@readBulk']);
        // Route::post('payrolls/bulk-save', ['as' => 'payrolls.save-bulk', 'role' => 'backend', 'uses' => 'PayrollController@saveBulk']);
        Route::resource('drivers', 'DriverController')->except(['show']);
        Route::get('drivers/create-bulk', ['as' => 'drivers.create-bulk', 'role' => 'backend', 'uses' => 'DriverController@createBulk']);
        Route::post('drivers/bulk-read', ['as' => 'drivers.read-bulk', 'role' => 'backend', 'uses' => 'DriverController@readBulk']);
        Route::get('drivers/download', ['as' => 'drivers.download', 'role' => 'backend', 'uses' => 'DriverController@download']);
        Route::post('drivers/bulk-save', ['as' => 'drivers.save-bulk', 'role' => 'backend', 'uses' => 'DriverController@saveBulk']);
        Route::get('drivers/detail/{id}', ['as' => 'drivers.detail', 'role' => 'backend', 'uses' => 'DriverController@detail']);

        Route::get('payoffs/select-tax', ['as' => 'payoffs.select-tax', 'role' => 'backend', 'uses' => 'PayOffController@selectTax']);
        Route::get('deductions/select-tax', ['as' => 'deductions.select-tax', 'role' => 'backend', 'uses' => 'DeductionController@selectTax']);
        Route::post('timekeepings/suat-an/{id}', ['role' => 'backend','as' => 'timekeepings.suat-an',  'uses' => 'TimekeepingController@tinhSuatAn']);
        Route::post('payrolls/bh/{id}', ['role' => 'backend','as' => 'payrolls.bh',  'uses' => 'PayrollController@bh']);
        Route::resource('insurances', 'InsuranceController')->except(['show']);
        Route::get('insurances/create-bulk', ['as' => 'insurances.create-bulk', 'role' => 'backend', 'uses' => 'InsuranceController@createBulk']);
        Route::get('insurances/download', ['as' => 'insurances.download', 'role' => 'backend', 'uses' => 'InsuranceController@download']);
        Route::post('insurances/bulk-save', ['as' => 'insurances.save-bulk', 'role' => 'backend', 'uses' => 'InsuranceController@saveBulk']);
        Route::post('insurances/bulk-read', ['as' => 'insurances.read-bulk', 'role' => 'backend', 'uses' => 'InsuranceController@readBulk']);
        Route::get('insurances/detail/{id}', ['as' => 'insurances.detail', 'role' => 'backend', 'uses' => 'InsuranceController@detail']);

        Route::get('payoffs/bulk-create', ['as' => 'payoffs.create-bulk', 'role' => 'backend', 'uses' => 'PayOffController@createBulk']);
        Route::get('payoffs/download', ['as' => 'payoffs.download', 'role' => 'backend', 'uses' => 'PayOffController@download']);
        Route::post('payoffs/bulk-read', ['as' => 'payoffs.read-bulk', 'role' => 'backend', 'uses' => 'PayOffController@readBulk']);
        Route::post('payoffs/bulk-save', ['as' => 'payoffs.save-bulk', 'role' => 'backend', 'uses' => 'PayOffController@saveBulk']);
        Route::resource('unionfunds', 'UnionFundController')->except(['show']);
        Route::get('unionfunds/searchUser', ['as' => 'unionfunds.searchUser', 'role' => 'backend', 'uses' => 'UnionFundController@searchUser']);
        Route::post('payrolls/thue/{id}', ['role' => 'backend','as' => 'payrolls.thue',  'uses' => 'PayrollController@thue']);
        // Route::get('payrolls/salary_user', ['as' => 'payrolls.salary_user',  'uses' => 'PayrollController@salaryUser']);
        Route::get('payrolls1/salary_user', ['as' => 'payrolls1.salary_user', 'role' => 'backend',  'uses' => 'PayrollController@salaryUser']);
        Route::post('contracts/cancel-concurrent', ['as' => 'contracts.cancel-concurrent', 'role' => 'admin.contracts.create', 'uses' => 'ContractController@cancelConcurrent']);
        Route::get('timekeepings/exportExcelOt/{id}', ['as' => 'timekeepings.exportExcelOt', 'role' => 'backend', 'uses' => 'TimekeepingController@exportExcelOt']);

        //bảng công update
        Route::resource('timekeepings', 'TimeKeepingV1Controller')->except('store');

        Route::post('timekeepings/store', ['as' => 'timekeepings.store', 'role' => 'timekeeping.create', 'uses' => 'TimeKeepingV1Controller@store']);

        Route::get('timekeepings/detail/{id}', ['as' => 'timekeepings.detail', 'role' => 'backend', 'uses' => 'TimeKeepingV1Controller@detail']);
        Route::post('timekeeping/update-timekeeping/{id}', ['as' => 'timekeeping.update-timekeeping', 'role' => 'timekeeping.create', 'uses' => 'TimeKeepingV1Controller@updateTimekeeping']);
        Route::get('ots/detail/{id}', ['as' => 'ots.detail', 'role' => 'backend', 'uses' => 'TimeKeepingV1Controller@otDetail']);
        Route::post('update/ots/{id}', ['role' => 'timekeeping.create','as' => 'ots.update',  'uses' => 'TimeKeepingV1Controller@updateOt']);
        Route::post('timekeeping/recalculate/{id}', ['role' => 'timekeeping.create','as' => 'timekeeping.recalculate',  'uses' => 'TimeKeepingV1Controller@recalculate']);

        //bảng lương update
        Route::resource('payroll', 'PayrollV1Controller')->only(['index', 'destroy']);
        Route::post('payroll/store', ['as' => 'payroll.store', 'role' => 'payrolls.create', 'uses' => 'PayrollV1Controller@store']);
        Route::get('payroll/detail/{id}', ['as' => 'payroll.detail', 'role' => 'payrolls.create', 'uses' => 'PayrollV1Controller@detail']);
        Route::post('payroll/store1', ['as' => 'payroll.store1', 'role' => 'payrolls.create', 'uses' => 'PayrollV1Controller@store1']);
        Route::get('payroll/exportExcel/{id}', ['as' => 'payroll.exportExcel', 'role' => 'payrolls.create', 'uses' => 'PayrollV1Controller@exportDepartment']);
        Route::get('timekeeping/exportExcel/{id}', ['as' => 'timekeeping.exportExcel', 'role' => 'backend', 'uses' => 'TimeKeepingV1Controller@exportExcel']);
        Route::get('timekeeping/exportExcel/log/{id}', ['as' => 'timekeeping.exportExcel.log', 'role' => 'backend', 'uses' => 'TimeKeepingV1Controller@exportExcelLog']);
        Route::get('timekeeping/exportExcelOt/{id}', ['as' => 'timekeeping.exportExcelOt', 'role' => 'backend', 'uses' => 'TimeKeepingV1Controller@exportExcelOt']);
        Route::get('payroll/exportCom', ['as' => 'payroll.exportCom', 'role' => 'payrolls.create', 'uses' => 'PayrollV1Controller@exportCom']);

        Route::post('manger/edit/admin', ['as' => 'manager.edit.admin', 'uses' => 'ManagerLeaveTakeController@editDayOffAdmin']);
        Route::get('timekeeping/log/{id}', ['as' => 'timekeeping.log', 'role' => 'backend', 'uses' => 'TimeKeepingV1Controller@getLog']);

        Route::get('payroll/payoff', ['as' => 'payroll.payoff', 'role' => 'backend', 'uses' => 'PayrollV1Controller@payOffUser']);
        Route::post('drivers/approved/{id}', ['as' => 'drivers.approved', 'role' => 'backend', 'uses' => 'DriverController@approved']);

        Route::get('list-logs/show-log', ['as' => 'list-logs.show-log', 'role' => 'backend', 'uses' => 'ListLogController@showLog']);
        Route::post('vans/approved/{id}', ['as' => 'vans.approved', 'role' => 'backend', 'uses' => 'VanController@approved']);

        // khoán chọn vỏ: 
        Route::post('salary-choose-containers/approved', ['as' => 'salary-choose-containers.approved', 'role' => 'backend', 'uses' => 'SalaryChooseContainerController@approved']);
        Route::post('salary-choose-containers/export-excel', ['as' => 'salary-choose-containers.exportExcel', 'uses' => 'SalaryChooseContainerController@exportExcel']);
        Route::post('salary-choose-containers/restart', ['as' => 'salary-choose-containers.restart', 'uses' => 'SalaryChooseContainerController@restart']);
        Route::get('salary-choose-containers/detail-dep', ['as' => 'salary-choose-containers.detailDep', 'uses' => 'SalaryChooseContainerController@detailWithDep']);
        Route::resource('salary-choose-containers', 'SalaryChooseContainerController');
        
        // khoán tờ khai
        Route::get('salary-declarations/detail-user', ['as' => 'salary-declarations.detailUser', 'role' => 'backend','uses' => 'SalaryDeclarationController@detailWithUser']);
        Route::get('salary-declarations/export-excel', ['as' => 'salary-declarations.exportExcel', 'role' => 'backend','uses' => 'SalaryDeclarationController@exportExcel']);
        Route::get('salary-declarations/create-salary-user', ['as' => 'salary-declarations.createSalaryUser', 'role' => 'backend','uses' => 'SalaryDeclarationController@createSalaryUser']);
        Route::get('salary-declarations/detail-com', ['as' => 'salary-declarations.detailCom', 'role' => 'backend', 'uses' => 'SalaryDeclarationController@detailWithCom']);
        Route::get('salary-declarations/detail-dep', ['as' => 'salary-declarations.detailDep', 'role' => 'backend', 'uses' => 'SalaryDeclarationController@detailWithDep']);
        Route::post('salary-declarations/save-salary-user', ['as' => 'salary-declarations.saveSalaryUser', 'role' => 'backend', 'uses' => 'SalaryDeclarationController@saveSalaryUser']);
        Route::post('salary-declarations/approved', ['as' => 'salary-declarations.approved', 'role' => 'backend', 'uses' => 'SalaryDeclarationController@approved']);
        Route::post('salary-declarations/restart', ['as' => 'salary-declarations.restart', 'role' => 'backend', 'uses' => 'SalaryDeclarationController@restart']);
        Route::resource('salary-declarations', 'SalaryDeclarationController');

    });
});

Route::group([], function () {
    Route::get('/', function () {
        return redirect()->route('admin.home');
    });
});
