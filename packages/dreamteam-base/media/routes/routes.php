<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

App::booted(function() {
	Route::group([
		'prefix'=> config('dreamteam_media.admin_dir'),
		'namespace' => 'DreamTeam\Media\Http\Controllers',
		'middleware' => config('dreamteam_media.middleware')
	], function () {
		// Route::get('media/view', 'MediaController@view')->name('media.view');
        // Route::post('media/upload-from-editor', 'MediaController@uploadFromEditor')->name('media.uploadFromEditor');
		// Route::resource('media', 'MediaController');
		Route::group(['prefix' => 'media', 'as' => 'media.'], function () {
			Route::get('', [
				'as' => 'index',
				'uses' => 'MediaController@getMedia',
			]);

			Route::get('popup', [
				'as' => 'popup',
				'uses' => 'MediaController@getPopup',
			]);

			Route::get('list', [
				'as' => 'list',
				'uses' => 'MediaController@getList',
			]);

			Route::get('breadcrumbs', [
				'as' => 'breadcrumbs',
				'uses' => 'MediaController@getBreadcrumbs',
			]);

			Route::post('global-actions', [
				'as' => 'global_actions',
				'uses' => 'MediaController@postGlobalActions',
			]);

			Route::post('download', [
				'as' => 'download',
				'uses' => 'MediaController@download',
			]);

			Route::post('getContent', [
				'as' => 'getContent',
				'uses' => 'MediaController@getContent',
			]);

			Route::group(['prefix' => 'files'], function () {
				Route::post('upload', [
					'as' => 'files.upload',
					'uses' => 'MediaFileController@postUpload',
				]);

				Route::post('upload-from-editor', [
					'as' => 'files.upload.from.editor',
					'uses' => 'MediaFileController@postUploadFromEditor',
				]);

				Route::post('download-url', [
					'as' => 'download_url',
					'uses' => 'MediaFileController@postDownloadUrl',
				]);
			});

			Route::group(['prefix' => 'folders'], function () {
				Route::post('create', [
					'as' => 'folders.create',
					'uses' => 'MediaFolderController@store',
				]);
			});
		});
	});
	Route::namespace('DreamTeam\Media\Http\Controllers')->name('admin.')->prefix(config('app.admin_dir'))->middleware(['web', 'auth-admin', '2fa'])->group(function() {
	    Route::name('settings.')->prefix('settings')->group(function() {
	        // Cấu hình media
			Route::prefix('media')->group(function () {
                Route::get('/', [
                    'as' => 'media',
                    'uses' => 'SettingController@edit',
                ]);

                Route::put('/', [
                    'as' => 'media.update',
                    'uses' => 'SettingController@update'
                ]);

                Route::post('generate-thumbnails', [
                    'as' => 'media.generate-thumbnails',
                    'uses' => 'SettingController@generateThumbnails'
                ]);
            });
	    });
	});
});
