<?php

return [

    'backup' => [

        'name' => env('APP_NAME', 'Laravel'),

        'source' => [

            'files' => [
                // ...
            ],

            'databases' => [
                'mysql',
            ],
        ],

        'destination' => [

            'disks' => [
                'local',
                's3',
                'google',
                // ...
            ],
        ],

        'database_dump_compressor' => null,

        'database_dump_method' => 'mysqldump',

        'mysql' => [
            'dump_command_path' => 'C:\xampp\mysql\bin\mysqldump.exe', // Path to mysqldump
            'dump_command_timeout' => 60 * 5, // 5 minutes
            'use_single_transaction',
            'timeout' => 60 * 5, // 5 minutes
        ],

        // ...
    ],

    // ...
];
