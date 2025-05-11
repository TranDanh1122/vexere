<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

App::booted(function () {
	$namespace = 'DreamTeam\Page\Http\Controllers';

	Route::namespace($namespace)->name('admin.')->prefix(config('app.admin_dir'))->middleware(['web', 'auth-admin', '2fa'])->group(function () {
		// Tài khoản người dùng quản trị
		Route::resource('pages', 'PageController');
		Route::delete('/pages/delete-forever/{id}', 'PageController@deleteForever')->name('pages.deleteForever');
	});
});
Route::middleware(['web'])->name('app.')->group(function () {
	Route::get('/pages/{slug}', 'DreamTeam\Page\Http\Controllers\PageController@show')->name('pages.show');
});
