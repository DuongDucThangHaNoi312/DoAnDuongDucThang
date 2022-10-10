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
        
        Route::group(['middleware' => ['admin.middleware']], function() {
            
            Route::get('', ['as' => 'home', 'uses' => 'HomeController@index']);
            Route::get('services', ['as' => 'services.index', 'uses' => 'ServiceController@index']);
            Route::get('equipments', ['as' => 'equipments.index', 'uses' => 'EquipmentController@index']);
            Route::get('departments', ['as' => 'departments.index', 'uses' => 'DepartmentsController@index']);
            Route::get('staffs', ['as' => 'staffs.index', 'uses' => 'StaffsController@index']);
            Route::resource('meeting-rooms', 'MeetingRoomController');
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
            
            // phòng ban
            Route::post('departments', ['as' => 'departments.store', 'uses' => 'DepartmentsController@store']);
            Route::get('departments/create', ['as' => 'departments.create', 'uses' => 'DepartmentsController@create']);
            Route::get('departments/{id}', ['as' => 'departments.show', 'uses' => 'DepartmentsController@show']);
            Route::get('departments/{id}/edit', ['as' => 'departments.edit', 'uses' => 'DepartmentsController@edit']);
            Route::put('departments/{id}', ['as' => 'departments.update', 'uses' => 'DepartmentsController@update']);
            Route::delete('departments/{id}', ['as' => 'departments.destroy', 'uses' => 'DepartmentsController@destroy']);
            
            // nhân viên
            Route::post('staffs', ['as' => 'staffs.store', 'uses' => 'StaffsController@store']);
            Route::get('staffs/create', ['as' => 'staffs.create', 'uses' => 'StaffsController@create']);
            Route::get('staffs/{id}', ['as' => 'staffs.show', 'uses' => 'StaffsController@show']);
            Route::get('staffs/{id}/edit', ['as' => 'staffs.edit', 'uses' => 'StaffsController@edit']);
            Route::put('staffs/{id}', ['as' => 'staffs.update', 'uses' => 'StaffsController@update']);
            Route::delete('staffs/{id}', ['as' => 'staffs.destroy', 'uses' => 'StaffsController@destroy']);
        });
    });
});

Route::group(['middleware' => ['admin.middleware']], function() {
    Route::get('/', function () {
        return redirect()->route('admin.home');
    });
});


Route::get('/listAPIS', ['as' => 'staffs.create', 'uses' => '\App\Http\Controllers\Backend\ListAPIController@getAllApi']);
