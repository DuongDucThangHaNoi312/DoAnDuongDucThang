<?php

use Illuminate\Http\Request;



Route::get('departments', 'Api\DepartmentController@index');
Route::post('departments', 'Api\DepartmentController@store');
Route::put('departments', 'Api\DepartmentController@update');
Route::delete('departments', 'Api\DepartmentController@destroy');


Route::get('meeting-rooms', 'Api\MeetingRoomController@index');
Route::post('meeting-rooms', 'Api\MeetingRoomController@store');
Route::put('meeting-rooms', 'Api\MeetingRoomController@update');
Route::delete('meeting-rooms', 'Api\MeetingRoomController@destroy');


Route::get('services', 'Api\ServiceController@index');
Route::post('services', 'Api\ServiceController@store');
Route::put('services', 'Api\ServiceController@update');
Route::delete('services', 'Api\ServiceController@destroy');


Route::get('equipments', 'Api\EquipmentController@index');
Route::post('equipments', 'Api\EquipmentController@store');
Route::put('equipments', 'Api\EquipmentController@update');
Route::delete('equipments', 'Api\EquipmentController@destroy');


Route::get('users', 'Api\UserController@index');
Route::post('users', 'Api\UserController@store');
Route::put('users', 'Api\UserController@update');
Route::delete('users', 'Api\UserController@destroy');
Route::post('login', 'Api\UserController@login');



Route::get('rentals', 'Api\RentalController@index');
Route::post('rentals', 'Api\RentalController@store');
Route::put('rentals', 'Api\RentalController@update');
Route::delete('rentals', 'Api\RentalController@destroy');