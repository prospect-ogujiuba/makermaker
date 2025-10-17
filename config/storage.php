<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Default Storage Location
    |--------------------------------------------------------------------------
    |
    | This option defines the default storage location that gets used when.
    | The name specified in this option should match one of the locations
    | defined in the "channels" configuration array.
    |
    */
    'default' => typerocket_env('TYPEROCKET_STORAGE_DEFAULT', 'storage'),

    /*
    |--------------------------------------------------------------------------
    | Storage Locations
    |--------------------------------------------------------------------------
    |
    | Here you may configure the storage locations for your application.
    |
    | Available Drivers: "stack", "local"
    */
    'drivers' => [
        'stack' => [
            'storage',
        ],

        'storage' => [
            'driver' => '\TypeRocket\Pro\Utility\Drives\StorageDrive',
        ],

        'uploads' => [
            'driver' => '\TypeRocket\Pro\Utility\Drives\UploadsDrive',
        ],

        'root' => [
            'driver' => '\TypeRocket\Pro\Utility\Drives\RootDrive',
        ],
    ],

];