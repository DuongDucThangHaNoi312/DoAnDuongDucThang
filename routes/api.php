<?php

use Illuminate\Http\Request;

Route::group(['prefix' => 'v1', 'namespace' => 'Api\v1'], function() {
    // Route::get('container.json', ['uses' => 'AuthController@qrcode']);
    // Route::get('containers.json', ['uses' => 'AuthController@search']);
    // Route::get('surveys.json', ['uses' => 'AuthController@survey']);
    // Route::get('secures.json', ['uses' => 'AuthController@secure']);
    // Route::post('containers/update', ['uses' => 'AuthController@update']);
    // Route::post('authenticate', ['as' => 'api.authenticate', 'uses' => 'AuthController@authenticate']);
    // Route::post('pre-containers/store', ['uses' => 'AuthController@storePreContainer']);
    // Route::post('secures/check', ['uses' => 'AuthController@checkSecure']);
    // Route::get('pre-containers.json', ['uses' => 'AuthController@preSearch']);
    // Route::get('pre-container.json', ['uses' => 'AuthController@preQrcode']);
});