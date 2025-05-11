<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

App::booted(function () {
	$namespace = 'DreamTeam\Translate\Http\Controllers';

	Route::namespace($namespace)->name('admin.')->prefix(config('app.admin_dir'))->middleware(['web', 'auth-admin'])->group(function () {
		// Language
		Route::group(['prefix' => 'settings/languages'], function () {
            Route::get('', [
                'as' => 'languages.index',
                'uses' => 'LanguageController@index',
            ]);

            Route::post('store', [
                'as' => 'languages.store',
                'uses' => 'LanguageController@store'
            ]);

            Route::post('edit', [
                'as' => 'languages.edit',
                'uses' => 'LanguageController@update'
            ]);

            Route::post('change-status', [
                'as' => 'languages.changeStatus',
                'uses' => 'LanguageController@changeStatus'
            ]);

            Route::get('get', [
                'as' => 'languages.get',
                'uses' => 'LanguageController@getLanguage'
            ]);

            Route::post('edit-setting', [
                'as' => 'languages.settings',
                'uses' => 'LanguageController@updateSetting'
            ]);
        });
        // Translations
        Route::group(['prefix' => 'translations'], function () {
            Route::group(['permission' => 'translations.index'], function () {
                Route::match(['GET', 'POST'], '', [
                    'as' => 'translations.index',
                    'uses' => 'TranslationController@index',
                ]);

                Route::post('edit', [
                    'as' => 'translations.group.edit',
                    'uses' => 'TranslationController@update'
                ]);

                Route::post('import', [
                    'as' => 'translations.import',
                    'uses' => 'TranslationController@import',
                ]);
            });
        });
	});

});
