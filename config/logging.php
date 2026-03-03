<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack'  => [
            'driver'            => 'stack',
            'channels'          => ['daily'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/laravel.log'),
            'level'  => 'debug',
        ],

        'daily'  => [
            'driver' => 'daily',
            'path'   => storage_path('logs/laravel.log'),
            'level'  => 'debug',
            'days'   => 30,
        ],

        'slack' => [
            'driver'   => 'slack',
            'url'      => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji'    => ':boom:',
            'level'    => 'critical',
        ],

        'papertrail' => [
            'driver'       => 'monolog',
            'level'        => 'debug',
            'handler'      => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver'    => 'monolog',
            'handler'   => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with'      => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level'  => 'debug',
        ],

        'errorlog'       => [
            'driver' => 'errorlog',
            'level'  => 'debug',
        ],

        //sql日志
        'sql'            => [
            'driver' => 'daily',
            'path'   => storage_path('logs/sql/laravel.log'),
            'level'  => 'debug',
            'days'   => 30,
        ],
        //http异常日志
        'http_exception' => [
            'driver' => 'daily',
            'path'   => storage_path('logs/http_exception/laravel.log'),
            'level'  => 'debug',
            'days'   => 30,
        ],
        //应用异常日志
        'app_exception'  => [
            'driver' => 'daily',
            'path'   => storage_path('logs/app_exception/laravel.log'),
            'level'  => 'debug',
            'days'   => 30,
        ],
        //cmd任务异常日志
        'task'           => [
            'driver' => 'daily',
            'path'   => storage_path('logs/task/laravel.log'),
            'level'  => 'debug',
            'days'   => 30,
        ],

    ],

];
