<?php

return [
    'sizes' => [
        'large' => '600x600',
        'medium' => '300x300',
        'small' => '150x150',
        'tiny' => '80x80'
    ],
    'thumbnail_module' => [
        'posts' => 'DreamTeam\\Post\\Services\\Interfaces\\PostServiceInterface'
    ],
    'permissions' => [
        'folders.create',
        'folders.edit',
        'folders.trash',
        'folders.destroy',
        'files.create',
        'files.edit',
        'files.trash',
        'files.destroy',
        'files.favorite',
        'folders.favorite',
    ],
    'libraries' => [
        'stylesheets' => [
            'vendor/core/core/media/libraries/jquery-context-menu/jquery.contextMenu.min.css',
            'vendor/core/core/base/plugins/lightbox/lightbox.min.css',
            'vendor/core/core/media/css/media.css?v=' . config('dreamteam_asset.version'),
        ],
        'javascript' => [
            'vendor/core/core/media/libraries/lodash/lodash.min.js',
            'vendor/core/core/media/libraries/jquery-context-menu/jquery.ui.position.min.js',
            'vendor/core/core/media/libraries/jquery-context-menu/jquery.contextMenu.min.js',
            'vendor/core/core/base/plugins/lightbox/fslightbox.js',
            'vendor/core/core/media/js/media.js?v=' . config('dreamteam_asset.version'),
        ],
    ],
    'allowed_mime_types' => env(
        'MEDIA_ALLOWED_MIME_TYPES',
        'jpg,jpeg,png,svg,gif,txt,docx,zip,mp3,bmp,csv,xls,xlsx,ppt,pptx,pdf,mp4,doc,mpga,wav,webp,webm,mov'
    ),
    'mime_types' => [
        'image' => [
            'image/png',
            'image/jpeg',
            'image/gif',
            'image/bmp',
            'image/svg+xml',
            'image/webp',
        ],
        'video' => [
            'video/mp4',
            'video/mov',
            'video/quicktime',
        ],
        'document' => [
            'application/pdf',
            'application/vnd.ms-excel',
            'application/excel',
            'application/x-excel',
            'application/x-msexcel',
            'text/plain',
            'application/msword',
            'text/csv',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ],
    ],
    'default_image' => env('MEDIA_DEFAULT_IMAGE', '/vendor/core/core/base/img/placeholder.png'),
    'sidebar_display' => env('MEDIA_SIDEBAR_DISPLAY', 'horizontal'), // Use "vertical" or "horizontal"
    'watermark' => [
        'enabled' => env('MEDIA_WATERMARK_ENABLED', 0),
        'source' => env('MEDIA_WATERMARK_SOURCE'),
        'size' => env('MEDIA_WATERMARK_SIZE', 10),
        'opacity' => env('MEDIA_WATERMARK_OPACITY', 80),
        'position' => env('MEDIA_WATERMARK_POSITION', 'bottom-right'),
        'x' => env('MEDIA_WATERMARK_X', 10),
        'y' => env('MEDIA_WATERMARK_Y', 10),
    ],

    'chunk' => [
        'enabled' => env('MEDIA_UPLOAD_CHUNK', false),
        'chunk_size' => 1024 * 1024, // Bytes
        'max_file_size' => 1024 * 1024, // MB

        /*
         * The storage config
         */
        'storage' => [
            /*
             * Returns the folder name of the chunks. The location is in storage/app/{folder_name}
             */
            'chunks' => 'chunks',
            'disk' => 'local',
        ],
        'clear' => [
            /*
             * How old chunks we should delete
             */
            'timestamp' => '-3 HOURS',
            'schedule' => [
                'enabled' => true,
                'cron' => '25 * * * *', // run every hour on the 25th minute
            ],
        ],
        'chunk' => [
            // setup for the chunk naming setup to ensure same name upload at same time
            'name' => [
                'use' => [
                    'session' => true, // should the chunk name use the session id? The uploader must send cookie!,
                    'browser' => false, // instead of session we can use the ip and browser?
                ],
            ],
        ],
    ],

    'preview' => [
        'document' => [
            'enabled' => env('MEDIA_DOCUMENT_PREVIEW_ENABLED', true),
            'providers' => [
                'google' => 'https://docs.google.com/gview?embedded=true&url={url}',
                'microsoft' => 'https://view.officeapps.live.com/op/view.aspx?src={url}',
            ],
            'default' => env('MEDIA_DOCUMENT_PREVIEW_PROVIDER', 'microsoft'),
            'type' => env('MEDIA_DOCUMENT_PREVIEW_TYPE', 'iframe'),          // use iframe or popup
            'mime_types' => [
                'application/pdf',
                'application/vnd.ms-excel',
                'application/excel',
                'application/x-excel',
                'application/x-msexcel',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ],
        ],
    ],
    'default_upload_folder' => env('MEDIA_DEFAULT_UPLOAD_FOLDER', public_path('uploads')),
    'default_upload_url' => env('MEDIA_DEFAULT_UPLOAD_URL', public_path('uploads')),
    'generate_thumbnails_enabled' => env('MEDIA_GENERATE_THUMBNAILS_ENABLED', true),
    'folder_colors' => [
        '#3498db',
        '#2ecc71',
        '#e74c3c',
        '#f39c12',
        '#9b59b6',
        '#1abc9c',
        '#34495e',
        '#e67e22',
        '#27ae60',
        '#c0392b',
    ],
    'use_storage_symlink' => env('MEDIA_USE_STORAGE_SYMLINK', false),

    /* Hình thức lưu: local | Server ảnh quy định trong config/FileSystem */
    'storage_type' => env('STORAGE_TYPE','local'),

    /* Linh ảnh cũ sẽ được thay bằng link ảnh mới đặt tại image_new nếu qua hàm getImage() */
    'image_old' => [env('LINK_IMAGE_OLD', '')],
    /* Linh ảnh mới sẽ được thay bằng link ảnh cũ đặt tại image_old nếu qua hàm getImage() */
    'image_new' => '',
    // fore old site
    'imageSize' => [
        'large' => 600,
        'medium' => 300,
        'small' => 150,
        'tiny' => 80,
    ],
    'imageRenderSize' => [
        'tripleextralarge' => 1520,
        'doubleextralarge' => 1250,
        'extralarge' => 900,
        'large' => 600,
        'mediumlarge' => 420,
        'medium' => 300,
        'small' => 150,
        'tiny' => 80,
        'little' => 30
    ],

    /* thư mục uploads trên local hoặc server */
    'folder' => env('FOLDER','uploads'),

    /* middleware. VD ['web', ...] */
    'middleware' => ['web', 'auth-admin'],

    /* đường dẫn admin */
    'admin_dir' => env('ADMIN_DIR', 'admin'),
];
