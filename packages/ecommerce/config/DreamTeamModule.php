<?php

return [
    'modules' => [
        'products' => [
            'name'          => 'Ecommerce::admin.product',
            'permision'     => [
                [ 'type' => 'index', 'name' => 'Core::admin.general.index' ],
                [ 'type' => 'create', 'name' =>  'Core::admin.general.create' ],
                [ 'type' => 'edit', 'name' =>  'Core::admin.general.edit'],
                [ 'type' => 'restore', 'name' => 'Core::admin.general.restore' ],
                [ 'type' => 'delete', 'name' =>  'Core::admin.general.delete'],
                [ 'type' => 'import', 'name' => 'Ecommerce::admin.import.name' ],
                [ 'type' => 'exports', 'name' => 'Ecommerce::admin.export_excel' ],
                [ 'type' => 'settings_summary_product', 'name' => 'Ecommerce::admin.setting_summary_product' ],
                [ 'type' => 'settings_interface_email_ecommerce', 'name' => 'Ecommerce::admin.setting_email' ],
                [ 'type' => 'settings_google_shoppings', 'name' => 'Ecommerce::admin.google_shopping.name' ],
                [ 'type' => 'settings_advanced', 'name' => 'Ecommerce::admin.setting_advanced' ],
                [ 'type' => 'deleteForever', 'name' => 'Core::admin.delete_forever' ],
                [ 'type' => 'warehouses', 'name' => 'Ecommerce::admin.warehouse' ],
            ],
        ],
        'product_categories' => [
            'name'          => 'Ecommerce::admin.product_category',
            'permision'     => [
                [ 'type' => 'index', 'name' => 'Core::admin.general.index' ],
                [ 'type' => 'create', 'name' =>  'Core::admin.general.create' ],
                [ 'type' => 'edit', 'name' =>  'Core::admin.general.edit'],
                [ 'type' => 'restore', 'name' => 'Core::admin.general.restore' ],
                [ 'type' => 'delete', 'name' =>  'Core::admin.general.delete'],
                [ 'type' => 'deleteForever', 'name' => 'Core::admin.delete_forever' ],
            ],
        ],
        'brands' => [
            'name'          => 'Ecommerce::admin.brand',
            'permision'     => [
                [ 'type' => 'index', 'name' => 'Core::admin.general.index' ],
                [ 'type' => 'create', 'name' =>  'Core::admin.general.create' ],
                [ 'type' => 'edit', 'name' =>  'Core::admin.general.edit'],
                [ 'type' => 'restore', 'name' => 'Core::admin.general.restore' ],
                [ 'type' => 'delete', 'name' =>  'Core::admin.general.delete'],
                [ 'type' => 'deleteForever', 'name' => 'Core::admin.delete_forever' ],
            ],
        ],
        'filters' => [
            'name'          => 'Ecommerce::admin.filter',
            'permision'     => [
                [ 'type' => 'index', 'name' => 'Core::admin.general.index' ],
                [ 'type' => 'create', 'name' =>  'Core::admin.general.create' ],
                [ 'type' => 'edit', 'name' =>  'Core::admin.general.edit'],
                [ 'type' => 'restore', 'name' => 'Core::admin.general.restore' ],
                [ 'type' => 'delete', 'name' =>  'Core::admin.general.delete'],
                [ 'type' => 'deleteForever', 'name' => 'Core::admin.delete_forever' ],
            ],
        ],
        'filter_details' => [
            'name'          => 'Chi tiết bộ lọc',
            'permision'     => [
                [ 'type' => 'index', 'name' => 'Core::admin.general.index' ],
                [ 'type' => 'delete', 'name' =>  'Core::admin.general.delete'],
                [ 'type' => 'deleteForever', 'name' => 'Core::admin.delete_forever' ],
            ],
        ],
        // 'flash_sales' => [
        //     'name'          => 'Ecommerce::admin.flashsale',
        //     'permision'     => [
        //         [ 'type' => 'index', 'name' => 'Core::admin.general.index' ],
        //         [ 'type' => 'create', 'name' =>  'Core::admin.general.create' ],
        //         [ 'type' => 'edit', 'name' =>  'Core::admin.general.edit'],
        //         [ 'type' => 'restore', 'name' => 'Core::admin.general.restore' ],
        //         [ 'type' => 'delete', 'name' =>  'Core::admin.general.delete'],
        //         [ 'type' => 'deleteForever', 'name' => 'Core::admin.delete_forever' ],
        //     ],
        // ],
        'orders' => [
            'name'          => 'Ecommerce::admin.order',
            'permision'     => [
                [ 'type' => 'index', 'name' => 'Core::admin.general.index' ],
                [ 'type' => 'create', 'name' =>  'Core::admin.general.create' ],
                [ 'type' => 'edit', 'name' =>  'Core::admin.general.edit'],
                [ 'type' => 'restore', 'name' => 'Core::admin.general.restore' ],
                [ 'type' => 'show', 'name' => 'Core::admin.general.view_detail' ],
                [ 'type' => 'export', 'name' => 'Ecommerce::admin.export_excel' ],
                [ 'type' => 'settings_payment_method', 'name' => 'Ecommerce::admin.config_payment' ],
                [ 'type' => 'deleteForever', 'name' => 'Core::admin.delete_forever' ],
            ],
        ],
        'order_details' => [
            'name'          => 'Ecommerce::admin.order_detail',
            'permision'     => [
                [ 'type' => 'index', 'name' => 'Core::admin.general.index' ],
                [ 'type' => 'export', 'name' => 'Ecommerce::admin.export_excel' ],
            ],
        ],
        'shipping' => [
            'name'          => 'Ecommerce::product.payment.shipping_method',
            'permision'     => [
                [ 'type' => 'settings_shipping_method', 'name' => 'Ecommerce::product.payment.shipping_method' ],
            ],
        ],
    ]
];
