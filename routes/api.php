<?php

use Illuminate\Http\Request;

Route::group(['prefix' => 'v1', 'namespace' => 'Api\v1'], function() {
    Route::get('departments', ['data' => 'DepartmentController@index']);

});