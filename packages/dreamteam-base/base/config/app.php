<?php 

return [
	// Đường dẫn Admin
	'admin_dir' => env('ADMIN_DIR', 'admin'),

	// Các trạng thái chung trên toàn trang
	'status' => [
		'2' => 'Core::admin.general.save_draf',
        '1' => 'Core::admin.general.active',
		'0' => 'Core::admin.general.non_active',
	],

	'page_size' => [ 10, 30, 50, 100 ],

    'enable_less_secure_web' => env('CMS_ENABLE_LESS_SECURE_WEB', false),
    'enable_ini_set' => env('CMS_ENABLE_INI_SET', true),
    'google_fonts_url' => env('CMS_GOOGLE_FONTS_URL', 'https://fonts.bunny.net'),
    'google_fonts_enabled_cache' => env('CMS_GOOGLE_FONTS_ENABLED_CACHE', true),
    'enable_system_updater' => env('CMS_ENABLE_SYSTEM_UPDATER', true),
    'enable_marketplace_feature' => env('CMS_ENABLE_MARKETPLACE_FEATURE', true),
];
