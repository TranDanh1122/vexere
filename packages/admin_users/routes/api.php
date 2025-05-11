<?php

use Illuminate\Support\Facades\Route;
$namespace = 'DreamTeam\AdminUser\Http\Controllers\Api';
	
Route::namespace($namespace)->middleware('api')->prefix('api/v1')->name('api.')->group(function () {
    Route::post('login', 'AuthenticationController@login');

    Route::post('password/forgot', 'ForgotPasswordController@sendResetLinkEmail');
    
    Route::middleware('auth:sanctum')->group(function() {
        Route::get('logout', 'AuthenticationController@logout');
        Route::get('/me', 'ProfileController@getProfile')->name('getProfile');
        Route::put('me', 'ProfileController@updateProfile');
        Route::post('update/avatar', 'ProfileController@updateAvatar');
        Route::put('update/password', 'ProfileController@updatePassword');
    });
});