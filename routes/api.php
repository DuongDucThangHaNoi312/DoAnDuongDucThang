<?php

use Illuminate\Http\Request;

Route::apiResource('departments', 'Api\DepartmentController');
Route::apiResource('users', 'Api\UserController');
Route::apiResource('meeting-rooms', 'Api\MeetingRoomController');
Route::apiResource('equipments', 'Api\EquipmentController');
Route::apiResource('services', 'Api\ServiceController');