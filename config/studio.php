<?php

use InertiaStudio\Laravel\Icons\HeroiconsProvider;
use InertiaStudio\Laravel\Icons\LucideProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Frontend Framework
    |--------------------------------------------------------------------------
    |
    | The frontend framework used by the application. Auto-detected during
    | studio:install from package.json, or set explicitly here.
    |
    */

    'framework' => 'react', // 'react' | 'vue' | 'svelte'

    /*
    |--------------------------------------------------------------------------
    | Default Panel Path
    |--------------------------------------------------------------------------
    |
    | The default URL prefix for the admin panel. Each panel can override
    | this with its own $path property.
    |
    */

    'path' => '/admin',

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    |
    | Default authentication guard and middleware for Studio panels.
    | Each panel can override these individually.
    |
    */

    'guard' => 'web',

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | File Uploads
    |--------------------------------------------------------------------------
    |
    | Default disk and directory for file uploads. Individual fields
    | can override these with ->disk() and ->directory().
    |
    */

    'uploads' => [
        'disk' => 'public',
        'directory' => 'studio-uploads',
    ],

    /*
    |--------------------------------------------------------------------------
    | Icons
    |--------------------------------------------------------------------------
    |
    | Default icon provider and registered providers. Use the second
    | argument on ->icon('name', 'provider') to override per-usage.
    |
    */

    'icons' => [
        'default' => 'heroicons',

        'providers' => [
            'heroicons' => HeroiconsProvider::class,
            'lucide' => LucideProvider::class,
        ],
    ],

];
