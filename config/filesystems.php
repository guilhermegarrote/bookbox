<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as various cloud based
    | disks, are available to your application for file storage.
    |
    */
    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | You may configure as many filesystem "disks" as you wish, and even
    | configure multiple disks of the same driver. Examples of popular
    | supported drivers are provided below.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'), // directory for private files
            'serve' => true,                        // allow serving files directly if needed
            'throw' => false,                        // whether exceptions are thrown on errors
            'report' => false,                       // control error reporting
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),    // directory for public files
            'url' => env('APP_URL') . '/storage',    // public URL for external access
            'visibility' => 'public',                // files are publicly accessible
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        'labels' => [
            'driver' => 'local',
            'root' => storage_path('app/labels'),
            'visibility' => 'private',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `php artisan storage:link` command is executed. The array keys should
    | be the locations of the links and the values should be their targets.
    |
    */
    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];
