<?php

namespace DreamTeam\AdminUser\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use DB;

class AdminUserSeedCommand extends Command {

    protected $signature = 'admin_users:seeds {--force}';

    protected $description = 'Khởi tạo dữ liệu cho tài khoản quản trị';

    public function handle() {
        if($this->option('force') || $this->confirm(__('AdminUser::admin.confirm_command'))) {
            $this->adminUserRoles();
            $this->adminUsers();
        }
    }

    public function echoLog($string) {
        $this->info($string);
        Log::info($string);
    }

    public function adminUsers() {
    	DB::table('admin_users')->truncate();
        DB::table('slugs')->where('table', 'admin_users')->delete();
    	DB::table('admin_password_resets')->truncate();
    	$created_at = $updated_at = date('Y-m-d H:i:s');
    	$user_name_array = [
    		// [
    		// 	'name' => 'dev',
    		// 	'email' => 'dev@dreamteam',
    		// 	'password' => passwordGenerate(),
    		// 	'display_name' => 'DreamTeam Developer',
    		// ],
    		[
    			'name' => 'dreamteam',
    			'email' => 'info@dreamteam',
    			'password' => passwordGenerate(),
    			'display_name' => 'Dream Team'
    		]
    	];
    	$admin_users = [];
    	foreach ($user_name_array as $key => $value) {
    		$admin_users[] = [
	            'slug' => str_slug($value['name']),
	            'name' => $value['name'],
	            'email' => $value['email'],
	            'password' => bcrypt($value['password']),
	            'display_name' =>$value['display_name'],
	            'status' => 1,
                "is_supper_admin" => 1,
	            'created_at' => $created_at,
	            'updated_at' => $updated_at
    		];
            DB::table('slugs')->insert([
                'slug' => str_slug($value['name']),
                'table' => 'admin_users',
                'table_id' => $key + 1,
                'created_at' => $created_at,
                'updated_at' => $updated_at
            ]);
    	}
        DB::table('admin_users')->insert($admin_users);
        $this->echoLog('Tai khoan quan tri da duoc tao tu dong:');
        foreach ($user_name_array as $value) {
        	$this->echoLog($value['name'].' - '.$value['password']);
        }
    }

    public function adminUserRoles() {
        DB::table('admin_user_roles')->truncate();
        $created_at = $updated_at = date('Y-m-d H:i:s');
        $role_array = [
            [
                'name' => 'Quản trị',
                'permisions' => '["on", "settings_theme_config", "settings_general", "settings_home", "settings_other", "settings_overview", "settings_email", "settings_code", "settings_googleAuthenticate", "settings_siteLanguage", "settings_link_custom", "settings_custom_css", "settings_toc", "settings_reading", "settings_ads", "settings_call_to_action", "settings_currency", "on", "sb_appointments_index", "sb_appointments_create", "sb_appointments_edit", "sb_appointments_restore", "sb_appointments_show", "sb_appointments_export", "sb_appointments_delete", "sb_appointments_deleteForever", "sb_appointments_calendar", "sb_appointments_setting_service_booking_base", "on", "sb_branches_index", "sb_branches_create", "sb_branches_edit", "sb_branches_restore", "sb_branches_show", "sb_branches_delete", "sb_branches_deleteForever", "on", "sb_services_index", "sb_services_create", "sb_services_edit", "sb_services_restore", "sb_services_show", "sb_services_delete", "sb_services_deleteForever", "on", "products_index", "products_create", "products_edit", "products_restore", "products_delete", "products_import", "products_exports", "settings_summary_product", "settings_interface_email_ecommerce", "settings_google_shoppings", "settings_advanced", "products_deleteForever", "products_warehouses", "on", "product_categories_index", "product_categories_create", "product_categories_edit", "product_categories_restore", "product_categories_delete", "product_categories_deleteForever", "on", "brands_index", "brands_create", "brands_edit", "brands_restore", "brands_delete", "brands_deleteForever", "on", "filters_index", "filters_create", "filters_edit", "filters_restore", "filters_delete", "filters_deleteForever", "on", "filter_details_index", "filter_details_delete", "filter_details_deleteForever", "on", "flash_sales_index", "flash_sales_create", "flash_sales_edit", "flash_sales_restore", "flash_sales_delete", "flash_sales_deleteForever", "on", "orders_index", "orders_create", "orders_edit", "orders_restore", "orders_show", "orders_export", "settings_payment_method", "orders_deleteForever", "on", "order_details_index", "order_details_export", "on", "settings_shipping_method", "on", "users_index", "users_show", "settings_user_base", "on", "countries_index", "countries_create", "countries_edit", "countries_restore", "countries_delete", "countries_deleteForever", "on", "provinces_index", "provinces_create", "provinces_edit", "provinces_restore", "provinces_delete", "provinces_deleteForever", "on", "districts_index", "districts_create", "districts_edit", "districts_restore", "districts_delete", "districts_deleteForever", "on", "wards_index", "wards_create", "wards_edit", "wards_restore", "wards_delete", "wards_deleteForever", "on", "location_bulk-import_index", "location_export_index", "on", "estates_index", "estates_create", "estates_edit", "estates_restore", "estates_delete", "estates_deleteForever", "on", "estates_categories_index", "estates_categories_create", "estates_categories_edit", "estates_categories_restore", "estates_categories_delete", "estates_categories_deleteForever", "on", "em_investors_index", "em_investors_create", "em_investors_edit", "em_investors_restore", "em_investors_delete", "em_investors_deleteForever", "on", "em_labels_index", "em_labels_create", "em_labels_edit", "em_labels_restore", "em_labels_delete", "em_labels_deleteForever", "on", "em_label_business_index", "em_label_business_create", "em_label_business_edit", "em_label_business_restore", "em_label_business_delete", "em_label_business_deleteForever", "on", "em_widgets_index", "em_widgets_create", "em_widgets_edit", "em_widgets_restore", "em_widgets_delete", "em_widgets_deleteForever", "on", "em_widget_details_index", "em_widget_details_delete", "on", "em_filters_index", "em_filters_create", "em_filters_edit", "em_filters_restore", "em_filters_delete", "em_filters_deleteForever", "on", "em_filter_details_index", "em_filter_details_delete", "on", "sync_nhanhvn_maps_index", "sync_nhanhvn_maps_create", "sync_nhanhvn_maps_edit", "sync_nhanhvn_maps_restore", "sync_nhanhvn_maps_delete", "sync_nhanhvn_maps_deleteForever", "settings_sync_nhanhvn", "settings_sync_nhanhvn_handle", "on", "magazine_volumes_index", "magazine_volumes_create", "magazine_volumes_edit", "magazine_volumes_restore", "magazine_volumes_delete", "magazine_volumes_deleteForever", "on", "magazine_issues_index", "magazine_issues_create", "magazine_issues_edit", "magazine_issues_restore", "magazine_issues_delete", "magazine_issues_deleteForever", "on", "magazines_index", "magazines_create", "magazines_edit", "magazines_restore", "magazines_delete", "magazines_deleteForever", "on", "ac_keywords_index", "ac_keywords_create", "ac_keywords_edit", "ac_keywords_restore", "ac_keywords_delete", "settings_general_ai", "ac_keywords_deleteForever", "on", "ac_prompts_index", "ac_prompts_create", "ac_prompts_edit", "ac_prompts_restore", "ac_prompts_delete", "ac_prompts_deleteForever", "on", "wordpress_import_import", "wordpress_import_import_image", "wordpress_import_import_crawl_data", "on", "slides_index", "slides_create", "slides_edit", "slides_restore", "slides_delete", "slides_deleteForever", "on", "forms_index", "forms_create", "forms_edit", "forms_restore", "forms_delete", "forms_deleteForever", "settings_recaptcha", "on", "form_subscribes_index", "on", "settings_checkseo_base", "on", "payments_index", "payments_edit", "payments_show", "payments_restore", "payments_delete", "payments_deleteForever", "settings_payments", "on", "comments_index", "comments_create", "comments_edit", "comments_restore", "comments_delete", "comments_deleteForever", "on", "widgets_index", "widgets_create", "widgets_edit", "widgets_delete", "on", "plugins_index", "plugins_create", "plugins_edit", "plugins_delete", "plugins_marketplace", "on", "themes_index", "themes_create", "themes_edit", "themes_delete", "themes_marketplace", "on", "posts_index", "posts_create", "posts_edit", "posts_restore", "posts_delete", "posts_deleteForever", "settings_postSetting", "on", "post_categories_index", "post_categories_create", "post_categories_edit", "post_categories_restore", "post_categories_delete", "post_categories_deleteForever", "on", "pages_index", "pages_create", "pages_edit", "pages_restore", "pages_delete", "pages_deleteForever", "on", "sync_links_index", "sync_links_create", "sync_links_edit", "sync_links_restore", "sync_links_delete", "sync_links_import", "sync_links_export", "sync_links_deleteForever", "on", "admin_users_index", "admin_users_create", "admin_users_edit", "admin_users_restore", "admin_users_delete", "on", "admin_user_roles_index", "admin_user_roles_create", "admin_user_roles_edit", "admin_user_roles_restore", "admin_user_roles_delete", "admin_user_roles_deleteForever", "on", "menus_index", "menus_create", "menus_edit", "menus_restore", "menus_delete", "menus_deleteForever", "on", "media_index", "settings_media", "on", "job_statuses_index", "job_statuses_delete", "on", "system_logs_index", "system_logs_show", "system_logs_delete", "system_logs_restore"]'
            ],
            [
                'name' => 'Biên tập',
                'permisions' => '["sb_appointments_index", "sb_appointments_create", "sb_appointments_edit", "sb_appointments_restore", "sb_appointments_show", "sb_appointments_export", "sb_appointments_delete", "sb_branches_index", "sb_branches_create", "sb_branches_edit", "sb_branches_restore", "sb_branches_show", "sb_branches_delete", "sb_services_index", "sb_services_create", "sb_services_edit", "sb_services_restore", "sb_services_show", "sb_services_delete", "products_index", "products_create", "products_edit", "products_restore", "products_delete", "products_import", "products_exports", "products_warehouses", "product_categories_index", "product_categories_create", "product_categories_edit", "product_categories_restore", "product_categories_delete", "brands_index", "brands_create", "brands_edit", "brands_restore", "brands_delete", "filters_index", "filters_create", "filters_edit", "filters_restore", "filters_delete", "filter_details_index", "filter_details_delete", "flash_sales_index", "flash_sales_create", "flash_sales_edit", "flash_sales_restore", "flash_sales_delete", "orders_index", "orders_create", "orders_edit", "orders_restore", "orders_show", "order_details_index", "order_details_export", "settings_shipping_method", "users_index", "users_show", "countries_index", "countries_create", "countries_edit", "countries_restore", "countries_delete", "provinces_index", "provinces_create", "provinces_edit", "provinces_restore", "provinces_delete", "districts_index", "districts_create", "districts_edit", "districts_restore", "districts_delete", "wards_index", "wards_create", "wards_edit", "wards_restore", "wards_delete", "location_bulk-import_index", "location_export_index", "estates_index", "estates_create", "estates_edit", "estates_restore", "estates_delete", "estates_categories_index", "estates_categories_create", "estates_categories_edit", "estates_categories_restore", "estates_categories_delete", "em_investors_index", "em_investors_create", "em_investors_edit", "em_investors_restore", "em_investors_delete", "em_labels_index", "em_labels_create", "em_labels_edit", "em_labels_restore", "em_labels_delete", "em_label_business_index", "em_label_business_create", "em_label_business_edit", "em_label_business_restore", "em_label_business_delete", "em_widgets_index", "em_widgets_create", "em_widgets_edit", "em_widgets_restore", "em_widgets_delete", "em_widget_details_index", "em_widget_details_delete", "em_filters_index", "em_filters_create", "em_filters_edit", "em_filters_restore", "em_filters_delete", "em_filter_details_index", "em_filter_details_delete", "sync_nhanhvn_maps_index", "sync_nhanhvn_maps_create", "sync_nhanhvn_maps_edit", "sync_nhanhvn_maps_restore", "sync_nhanhvn_maps_delete", "magazine_volumes_index", "magazine_volumes_create", "magazine_volumes_edit", "magazine_volumes_restore", "magazine_volumes_delete", "magazine_issues_index", "magazine_issues_create", "magazine_issues_edit", "magazine_issues_restore", "magazine_issues_delete", "magazines_index", "magazines_create", "magazines_edit", "magazines_restore", "magazines_delete", "ac_keywords_index", "ac_keywords_create", "ac_keywords_edit", "ac_keywords_restore", "ac_keywords_delete", "settings_general_ai", "ac_prompts_index", "ac_prompts_create", "ac_prompts_edit", "ac_prompts_restore", "ac_prompts_delete", "wordpress_import_import", "wordpress_import_import_image", "wordpress_import_import_crawl_data", "form_subscribes_index", "settings_checkseo_base", "payments_index", "payments_edit", "payments_show", "payments_restore", "payments_delete", "comments_index", "comments_create", "comments_edit", "comments_restore", "comments_delete", "posts_index", "posts_create", "posts_edit", "posts_restore", "posts_delete", "post_categories_index", "post_categories_create", "post_categories_edit", "post_categories_restore", "post_categories_delete", "pages_index", "pages_create", "pages_edit", "pages_restore", "pages_delete", "sync_links_index", "sync_links_create", "sync_links_edit", "sync_links_restore", "sync_links_delete", "sync_links_import", "sync_links_export"]'
            ],
            [
                'name' => 'Tác giả',
                'permisions' => '["posts_private", "posts_create", "posts_edit"]'
            ]
        ];
        $admin_user_roles = [];
        foreach ($role_array as $key => $value) {
            $admin_user_roles[] = [
                'name' => $value['name'],
                'permisions' => $value['permisions'],
                'status' => 1,
                'created_at' => $created_at,
                'updated_at' => $updated_at
            ];
        }
        DB::table('admin_user_roles')->insert($admin_user_roles);
        $this->echoLog('Vai tro da duoc tao tu dong');
        foreach ($role_array as $value) {
            $this->echoLog('- '. $value['name']);
        }
    }

}
