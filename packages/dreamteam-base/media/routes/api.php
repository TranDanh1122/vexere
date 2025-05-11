<?php

use Illuminate\Support\Facades\Route;
$namespace = 'DreamTeam\Media\Http\Controllers\Api';
	
Route::namespace($namespace)->middleware('api')->prefix('api/v1')->name('api.')->group(function () {
    Route::middleware('auth:sanctum')->prefix('medias')->group(function() {
        Route::post('/media', 'MediaController@uploadMedia');
    });
});