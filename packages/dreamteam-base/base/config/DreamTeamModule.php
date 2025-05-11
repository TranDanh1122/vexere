<?php 

return [
    'modules' => [
        'admin_users' => [
            'name'          => 'AdminUser::admin.admin_user_name',
            'permision'     => [
                [ 'type' => 'index', 'name' => 'Core::admin.general.index'],
                [ 'type' => 'create', 'name' =>  'Core::admin.general.create'],
                [ 'type' => 'edit', 'name' =>  'Core::admin.general.edit'],
                [ 'type' => 'restore', 'name' => 'Core::admin.general.restore'],
                [ 'type' => 'delete', 'name' =>  'Core::admin.general.delete'],
            ],
        ],
        'admin_user_roles' => [
            'name'          => 'AdminUser::admin.roles.name',
            'permision'     => [
                [ 'type' => 'index', 'name' => 'Core::admin.general.index'],
                [ 'type' => 'create', 'name' =>  'Core::admin.general.create'],
                [ 'type' => 'edit', 'name' =>  'Core::admin.general.edit'],
                [ 'type' => 'restore', 'name' => 'Core::admin.general.restore'],
                [ 'type' => 'delete', 'name' =>  'Core::admin.general.delete'],
                [ 'type' => 'deleteForever', 'name' => 'Core::admin.delete_forever' ],
            ],
        ],
        'settings' => [
            'name'          => 'Core::admin.admin_menu.config',
            'permision'     => [
                [ 'type' => 'overview', 'name' => 'Core::admin.setting.config'],
                [ 'type' => 'email', 'name' => 'Core::admin.admin_menu.serve_mail'],
                [ 'type' => 'googleAuthenticate', 'name' => 'Core::admin.admin_menu.googleAuthenticate'],
                [ 'type' => 'siteLanguage', 'name' => 'Translate::language.name'],
            ],
        ],
        'media' => [
            'name'          => 'Translate::media.title',
            'permision'     => [
                [ 'type' => 'index', 'name' => 'Core::admin.general.index'],
                [ 'type' => 'settings_media', 'name' => 'Core::admin.admin_menu.media_setting'],
            ],
        ],
        'job_statuses' => [
            'name'          => 'JobStatus::admin.progress',
            'permision'     => [
                [ 'type' => 'index', 'name' => 'Core::admin.general.index'],
                [ 'type' => 'delete', 'name' =>  'Core::admin.general.delete'],
            ],
        ],
        'system_logs' => [
            'name'          => 'Core::admin.admin_menu.system_logs',
            'permision'     => [
                [ 'type' => 'index', 'name' => 'Core::admin.general.index'],
                [ 'type' => 'show', 'name' => 'Core::admin.general.view_detail'],
                [ 'type' => 'delete', 'name' =>  'Core::admin.general.delete'],
                [ 'type' => 'restore', 'name' => 'Core::admin.general.restore'],
            ],
        ],
    ],
    // Tên cho modules hoặc permision (ưu tiên lấy tại modules)
    'name' => [
        'index'                 => 'Core::admin.general.index',
        'create'                => 'Core::admin.general.create',
        'show'                  => 'Core::admin.general.view_detail',
        'edit'                  => 'Core::admin.general.edit',
        'restore'               => 'Core::admin.general.restore',
        'delete'                => 'Core::admin.general.delete',
    ],
    
    // Hiển thị chi tiết tên cho các trường của từng bảng (Ưu tiên nếu không có sẽ lấy logs_name)
    'logs' => [
        'admin_users' => [
            'display_name'      => 'Core::admin.display_name',
            'avatar'            => 'Core::admin.general.avatar',
            'capabilities'      => 'Core::admin.capabilities',
        ],
    ],
    // Logs hiển thị tên các field chung của các bảng 
    'logs_name' => [
        'category_id'           => 'Core::admin.general.category',
        'parent_id'             => 'Core::admin.general.category_parent',
        'name'                  => 'Core::admin.general.name',
        'slug'                  => 'Core::admin.general.slug',
        'image'                 => 'Core::admin.general.avatar',
        'email'                 => 'Email',
        'phone'                 => 'Core::admin.general.phone',
        'price'                 => 'Core::admin.general.price',
        'price_old'             => 'Core::admin.general.price_old',
        'detail'                => 'Core::admin.general.content',
        'order'                 => 'Core::admin.general.order',
        'status'                => 'Core::admin.general.status',
        'created_at'            => 'Core::admin.general.created_at',
        'updated_at'            => 'Core::admin.general.update_time',
        'lang_locale'           => 'Langugae',
        'location'              => 'Position',
        'value'                 => 'Value',
        'meta_title'            => 'Meta title',
        'meta_description'      => 'Meta Description',
        'meta_social_title'     => 'Meta Robots',
        'meta_social_description' => 'Social Image',
        'meta_robots'             => 'Social Title',
        'meta_social_image'       => 'Social Description',
        'meta_html_head'          => 'Translate::form.metaseo.add_code_head',
        'meta_html_foot'          => 'Translate::form.metaseo.add_code_foot',
        'admin_user_id'           => 'Publisher',
        'image_slides'            => 'Image slides',
        'seo_point'               => 'Seo Point',
        'related_posts'           => 'Related post',
        'primary_keyword'         => 'Primary keyword',
        'secondary_keyword'       => 'Secondary keyword',
        'description'             => 'Description',
        'related_products'        => 'Related product',
        'quantity'                => 'Quantity',
        'length'                  => 'Length',
        'wide'                    => 'Wide',
        'height'                  => 'Height',
        'weight'                  => 'Weight',
        'slide'                   => 'Slide images',
        'brand_id'                => 'Brand'
    ],
    // Trường được set tại fields này là trường nội dung thì hiển thị sẽ là nội dung
    'logs_content_field' => [
        'capabilities',
        'detail', 
        'content',
    ],
];
