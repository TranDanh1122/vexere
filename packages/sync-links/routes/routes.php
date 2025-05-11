<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

App::booted(function () {
	$namespace = 'DreamTeam\SyncLink\Http\Controllers';

	Route::namespace($namespace)->name('admin.')->prefix(config('app.admin_dir'))->middleware(['web', 'auth-admin'])->group(function () {
		// Link đồng bộ
		Route::resource('sync_links', 'SyncLinkController');
		Route::delete('/sync_links/delete-forever/{id}', 'SyncLinkController@deleteForever')->name('sync_links.deleteForever');
	});

	Route::namespace($namespace)->name('admin.ajax.sync_links.')->prefix(config('app.admin_dir') . '/ajax')->middleware(['web', 'auth-admin'])->group(function () {
		// Import excel để thêm dữ liệu
		Route::post('sync_links/import', 'SyncLinkController@import')->name('import');
		// Export dữ liệu
		Route::post('sync_links/export', 'SyncLinkController@export')->name('export');
	});
});
