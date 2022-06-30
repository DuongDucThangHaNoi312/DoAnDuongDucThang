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
        Route::get('403', ['as' => '403', 'uses' => 'HomeController@get403']);
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
        Route::get('staff/update_password/{id}', ['as' => 'staffs.update_password', 'role' => 'admin.staffs.update', 'uses' => 'StaffsController@changePassword']);
        Route::put('staff/post_update_password/{id}', ['as' => 'staffs.update_password_put', 'role' => 'admin.staffs.update', 'uses' => 'StaffsController@postChangePassword']);
        Route::post('user/import', ['as' => 'user.import', 'role' => 'backend', 'uses' => 'UserExcelController@import']);
        Route::get('list-logs/show-log', ['as' => 'list-logs.show-log', 'role' => 'backend', 'uses' => 'ListLogController@showLog']);


        
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
        Route::get('departments/getShift/{departmentId}/{month}/{year}', ['as' => 'departments.getShift', 'role' => 'backend', 'uses' => 'CalendarDepartmentController@getShiftByMonth']);
        Route::get('departments/list/team/{departmentId}', ['as' => 'departments.list-team', 'role' => 'admin.departments.list-team', 'uses' => 'CalendarDepartmentController@listTeam']);
        Route::get('departments/create/team/{departmentId}', ['as' => 'departments.create-team', 'role' => 'admin.departments.create-team', 'uses' => 'CalendarDepartmentController@createTeam']);
        Route::post('departments/store/team', ['as' => 'departments.store-team', 'role' => 'admin.departments.create-team', 'uses' => 'CalendarDepartmentController@storeTeam']);
        Route::get('departments/users/team/{id}', ['as' => 'departments.users-team', 'role' => 'backend', 'uses' => 'CalendarDepartmentController@getAllUserTeam']);
        Route::get('departments/edit/team/{id}', ['as' => 'departments.edit-team', 'role' => 'admin.departments.edit-team', 'uses' => 'CalendarDepartmentController@editTeam']);
        Route::post('departments/save/team/{id}', ['as' => 'departments.save-edit-team', 'role' => 'admin.departments.edit-team', 'uses' => 'CalendarDepartmentController@saveEditTeam']);
        Route::delete('departments/delete/team/{id}', ['as' => 'departments.delete-team', 'role' => 'admin.departments.delete-team', 'uses' => 'CalendarDepartmentController@deleteTeam']);
       
        

        Route::get('services', ['as' => 'services.index', 'uses' => 'ServiceController@index']);
        Route::get('equipments', ['as' => 'equipments.index', 'uses' => 'EquipmentController@index']);

        Route::group(['middleware' => ['admin.middleware']], function() {
            // dịch vụ
            Route::post('services', ['as' => 'services.store', 'uses' => 'ServiceController@store']);
            Route::get('services/create', ['as' => 'services.create', 'uses' => 'ServiceController@create']);
            Route::get('services/{id}', ['as' => 'services.show', 'uses' => 'ServiceController@show']);
            Route::get('services/{id}/edit', ['as' => 'services.edit', 'uses' => 'ServiceController@edit']);
            Route::put('services/{id}', ['as' => 'services.update', 'uses' => 'ServiceController@update']);
            Route::delete('services/{id}', ['as' => 'services.destroy', 'uses' => 'ServiceController@destroy']);

            // trang thiết bị
            Route::post('equipments', ['as' => 'equipments.store', 'uses' => 'EquipmentController@store']);
            Route::get('equipments/create', ['as' => 'equipments.create', 'uses' => 'EquipmentController@create']);
            Route::get('equipments/{id}', ['as' => 'equipments.show', 'uses' => 'EquipmentController@show']);
            Route::get('equipments/{id}/edit', ['as' => 'equipments.edit', 'uses' => 'EquipmentController@edit']);
            Route::put('equipments/{id}', ['as' => 'equipments.update', 'uses' => 'EquipmentController@update']);
            Route::delete('equipments/{id}', ['as' => 'equipments.destroy', 'uses' => 'EquipmentController@destroy']);
        });
    });
});

Route::group([], function () {
    Route::get('/', function () {
        return redirect()->route('admin.home');
    });
});
