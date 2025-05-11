<?php

use Illuminate\Support\Facades\Route;

$namespace = 'DreamTeam\Base\Http\Controllers';

Route::namespace($namespace)->name('admin.')->prefix(config('app.admin_dir'))->middleware(['web', 'auth-admin', '2fa'])->group(function () {
	Route::get('/', 'DashboardController@index')->name('home');
	Route::resource('system_logs', 'SystemLogController');
	Route::resource('menus', 'MenuController');
	Route::delete('/menus/delete-forever/{id}', 'MenuController@deleteForever')->name('menus.deleteForever');
	Route::post('/system_logs/rollback/{id}', 'SystemLogController@rollback')->name('system_logs.rollback');
	Route::post('/system_logs/deleteWithRequest', 'SystemLogController@deleteWithRequest')->name('system_logs.deleteWithRequest');
	// cấu hình
	Route::name('settings.')->prefix('settings')->group(function () {
		// Cấu hình tổng quan
		Route::get('group-interface', 'SettingController@groupInterface')->name('groupInterface');
		Route::get('group-config', 'SettingController@groupConfig')->name('groupConfig');
		Route::match(['GET', 'POST'], 'overview', 'SettingController@overview')->name('overview');
		// Cấu hình email
		Route::match(['GET', 'POST'], 'email', 'SettingController@email')->name('email');
		// Cấu hình nội dung email
		Route::match(['GET', 'POST'], 'email-contents', 'SettingController@emailContents')->name('email_contents');
		// Cấu hình mã chuyển đổi
		Route::match(['GET', 'POST'], 'code', 'SettingController@code')->name('code');
		// Bat tat 2fa
		Route::match(['GET', 'POST'], 'google-authenticate', 'SettingController@googleAuthenticate')->name('googleAuthenticate');

		// Cấu hình link
		Route::match(['GET', 'POST'], 'link_custom', 'SettingController@link_custom')->name('link_custom');
		// custom css
		Route::match(['GET', 'POST'], 'custom-css', 'SettingController@custom_css')->name('custom_css');
		// toc setting
		Route::match(['GET', 'POST'], 'toc', 'SettingController@toc')->name('toc');
		// reading
		Route::match(['GET', 'POST'], 'reading', 'SettingController@reading')->name('reading');
		// Cấu hình trang liên hệ
		Route::match(['GET', 'POST'], 'contact', 'SettingController@contact')->name('contact');
		//khác
		Route::match(['GET', 'POST'], 'other', 'SettingController@other')->name('other');
		// custom ads.txt
		Route::match(['GET', 'POST'], 'ads', 'SettingController@ads')->name('ads');
		// custom icon
		Route::match(['GET', 'POST'], 'call-to-action', 'SettingController@callToAction')->name('call_to_action');
		// custom icon
		Route::match(['GET', 'POST'], 'tracking', 'SettingController@tracking')->name('tracking');
		// custom currency
		Route::match(['GET', 'POST'], 'currency', 'SettingController@currency')->name('currency');
		Route::post('mail_configs/test_mail', 'MailConfigController@testMail')->name('test_mail');
		Route::match(['GET', 'POST'], 'theme-config', 'SettingController@themeConfig')->name('theme_config');
        Route::match(['GET', 'POST'], 'general', 'SettingController@general')->name('general');
        Route::match(['GET', 'POST'], 'home', 'SettingController@home')->name('home');
	});
});

Route::namespace($namespace)->name('admin.ajax.')->prefix(config('app.admin_dir') . '/ajax')->middleware(['web', 'auth-admin', '2fa'])->group(function () {
	// Xóa cache
	Route::post('cache_clear', 'AdminController@cacheClear')->name('cache_clear');
	// Xóa nhanh
	Route::post('quick_delete', 'AdminController@quickDelete')->name('quick_delete');
	// Cập nhật nhanh
	Route::post('quick_edit', 'AdminController@quickEdit')->name('quick_edit');
	// Cập nhật nhanh ghim
	Route::post('quick_pin_edit', 'AdminController@quickPinEdit')->name('quick_pin_edit');
	// Lấy lại nhanh
	Route::post('quick_restore', 'AdminController@quickRestore')->name('quick_restore');
	// Kiểm tra tồn tại slug
	Route::post('check_slug', 'AdminController@checkSlug')->name('check_slug');
	// Tìm kiếm tại Form
	Route::post('suggest_search', 'AdminController@suggestSearch')->name('suggest_search');
	// Tìm kiếm tại bảng
	Route::post('suggest_table', 'AdminController@suggestTable')->name('suggest_table');
	Route::post('sussget_menu', 'AdminController@sussgetMenu')->name('sussget_menu');
});

Route::namespace($namespace)->prefix('/marketplace-api')->middleware(['check-auth-marketplace'])->group(function () {
	Route::post('update-license', 'SystemManagerment@updateLicense');
});
