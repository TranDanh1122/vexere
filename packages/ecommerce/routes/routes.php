<?php

use Illuminate\Support\Facades\Route;

$namespace = 'DreamTeam\Ecommerce\Http\Controllers';

Route::namespace($namespace)->name('admin.')->prefix(config('app.admin_dir'))->middleware(['web', 'auth-admin', '2fa'])->group(function () {
	// Sản phẩm
	Route::resource('products', 'ProductController');

	// Thương hiệu
	Route::resource('brands', 'BrandController');
	// Location
	Route::resource('locations', 'LocationController');
	// Region
	Route::resource('regions', 'RegionController');
	// Đơn hàng
	Route::resource('orders', 'OrderController')->only(['index', 'show']);
	// Đơn hàng chi tiết
	Route::resource('order_details', 'OrderDetailController');
	// Khách hàng
	Route::resource('customers', 'CustomerController');

	// route delete
	Route::delete('/products/delete-forever/{id}', 'ProductController@deleteForever')->name('products.deleteForever');
	Route::delete('/product_categories/delete-forever/{id}', 'ProductCategoryController@deleteForever')->name('product_categories.deleteForever');
	Route::delete('/brands/delete-forever/{id}', 'BrandController@deleteForever')->name('brands.deleteForever');
	Route::delete('/flash_sales/delete-forever/{id}', 'FlashSaleController@deleteForever')->name('flash_sales.deleteForever');
	Route::delete('/filters/delete-forever/{id}', 'FilterController@deleteForever')->name('filters.deleteForever');
	Route::delete('/filter_details/delete-forever/{id}', 'FilterController@deleteForeverFilterDetail')->name('filter_details.deleteForever');
	Route::delete('/locations/delete-forever/{id}', 'LocationController@deleteForever')->name('locations.deleteForever');
	// Bộ lọc
	Route::resource('filters', 'FilterController');
	Route::post('/ajax/get_product_filters', 'ProductController@getFilter')->name('products.filters');

	Route::name('orders.')->prefix('orders')->group(function(){

		Route::post('/{order_id}/admin_note', 'OrderController@adminNote')->name('admin_note');
	});
});
