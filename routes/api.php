<?php

use Illuminate\Http\Request;



Route::get('departments', 'Api\DepartmentController@index');
Route::post('departments', 'Api\DepartmentController@store');
Route::post('departments', 'Api\DepartmentController@store');
Route::delete('departments', 'Api\DepartmentController@destroy');


Route::get('meeting-rooms', 'Api\MeetingRoomController@index');
Route::post('meeting-rooms', 'Api\MeetingRoomController@store');
Route::post('meeting-rooms', 'Api\MeetingRoomController@store');
Route::delete('meeting-rooms', 'Api\MeetingRoomController@destroy');


Route::get('services', 'Api\ServiceController@index');
Route::post('services', 'Api\ServiceController@store');
Route::post('services', 'Api\ServiceController@store');
Route::delete('services', 'Api\ServiceController@destroy');


Route::get('equipments', 'Api\EquipmentController@index');
Route::post('equipments', 'Api\EquipmentController@store');
Route::post('equipments', 'Api\EquipmentController@store');
Route::delete('equipments', 'Api\EquipmentController@destroy');


Route::get('users', 'Api\UserController@index');
Route::post('users', 'Api\UserController@store');
Route::post('users', 'Api\UserController@store');
Route::delete('users', 'Api\UserController@destroy');
Route::post('login', 'Api\UserController@login');
Route::post('getUserSameDep', 'Api\UserController@getUserSameDep');



Route::get('rentals', 'Api\RentalController@index');
Route::get('rentals_get_metting_room_of_user', 'Api\RentalController@getMettingRoomOfUser');
Route::post('rentals', 'Api\RentalController@store');
Route::post('rentals', 'Api\RentalController@store');
Route::delete('rentals', 'Api\RentalController@destroy');
Route::get('room_empty', 'Api\RentalController@getRoomEmpty');