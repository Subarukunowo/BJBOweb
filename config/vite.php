<?php

return [

    'entrypoints' => [
        'resources/js/app.js',
    ],
    'ignore_missing_entrypoints' => false,

    /*
    |--------------------------------------------------------------------------
    | Entry Points
    |--------------------------------------------------------------------------
    |
    | Vite processes multiple entry points. Specify the files that should
    | be considered as entry points for your application.
    |
    */

    'input' => [
        'resources/js/app.js',
        'resources/css/app.css',
    ],

    /*
    |--------------------------------------------------------------------------
    | Server
    |--------------------------------------------------------------------------
    |
    | During development, the Vite development server provides HMR
    | (Hot Module Replacement). Configure the server as needed.
    |
    */

    'server' => [
        'host' => 'localhost',
        'port' => 5173,
    ],

    /*
    |--------------------------------------------------------------------------
    | Build Directory
    |--------------------------------------------------------------------------
    |
    | The directory where Vite will place the production-ready files.
    | Make sure this matches the output path in your vite.config.js.
    |
    */

    'build_path' => 'build',

];
