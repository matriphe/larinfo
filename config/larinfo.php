<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Converter
    |--------------------------------------------------------------------------
    |
    | This configuration to set the size converter.
    | Precision is the decimal precision.
    | Use binary to get unit in binary (KiB) instead of decimal (KB).
    | Example:
    | - precision = 2, use_binary = false, will result 123.45 MB
    | - precision = 1, use_binary = true, will result 123.5 MiB
    |
    */

    'converter' => [
        'precision' => 1,
        'use_binary' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Linfo Settings
    |--------------------------------------------------------------------------
    |
    | This value is used to set the Linfo. You might not need to edit this.
    |
    */

    'linfo' => [
        'show' => [
            'kernel' => true,
            'os' => true,
            'ram' => true,
            'mounts' => true,
            'webservice' => true,
            'phpversion' => true,
            'uptime' => true,
            'cpu' => true,
            'distro' => true,
            'model' => true,
            'virtualization' => true,
            'duplicate_mounts' => false,
            'mounts_options' => false,
        ],
    ],
];
