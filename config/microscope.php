<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable / Disable Microscope
    |--------------------------------------------------------------------------
    |
    | You can disable Microscope completely using .env
    | MICROSCOPE_ENABLED=false
    |
    */
    'is_enabled' => env('MICROSCOPE_ENABLED', true),


    /*
    |--------------------------------------------------------------------------
    | Prevent Auto Fix (Recommended: true)
    |--------------------------------------------------------------------------
    |
    | When true, Microscope will NOT automatically modify your files.
    | It will only report errors.
    |
    */
    'no_fix' => true,


    /*
    |--------------------------------------------------------------------------
    | Ignore Paths
    |--------------------------------------------------------------------------
    |
    | These paths will be ignored while scanning.
    | Useful to avoid vendor, cache, storage etc.
    |
    */
    'ignore' => [
        'vendor*',
        'storage*',
        'bootstrap/cache*',
        'node_modules*',
    ],


    /*
    |--------------------------------------------------------------------------
    | Detect Unused View Variables
    |--------------------------------------------------------------------------
    |
    | If true, Microscope detects variables passed to Blade but not used.
    |
    */
    'log_unused_view_vars' => true,


    /*
    |--------------------------------------------------------------------------
    | Ignored Namespaces
    |--------------------------------------------------------------------------
    |
    | Add any namespace here that should not be scanned.
    |
    */
    'ignored_namespaces' => [
        // 'Laravel\\Nova\\',
    ],


    /*
    |--------------------------------------------------------------------------
    | Class Search Buffer
    |--------------------------------------------------------------------------
    |
    | Increase if you have large controllers with many use statements.
    |
    */
    'class_search_buffer' => 4000,


    /*
    |--------------------------------------------------------------------------
    | Action Comment Template
    |--------------------------------------------------------------------------
    */
    'action_comment_template' => 'microscope_package::actions_comment',


    /*
    |--------------------------------------------------------------------------
    | Additional Route Files
    |--------------------------------------------------------------------------
    |
    | If you use custom route files, add them here.
    |
    */
    'additional_route_files' => [
        // base_path('routes/admin.php'),
        // base_path('routes/api_custom.php'),
    ],


    /*
    |--------------------------------------------------------------------------
    | Additional Config Paths
    |--------------------------------------------------------------------------
    */
    'additional_config_paths' => [
        // 'custom/config/path'
    ],


    /*
    |--------------------------------------------------------------------------
    | Additional Composer Files
    |--------------------------------------------------------------------------
    */
    'additional_composer_paths' => [
        // base_path('modules/module1/composer.json')
    ],

];
