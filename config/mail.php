<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Default Mail Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send any email
    | messages sent by your application. Alternative mailers may be setup
    | and used as needed; however, this mailer will be used by default.
    |
    */
    'default' => typerocket_env('TYPEROCKET_MAIL_DEFAULT', 'wp'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Options: "mailgun", "wp", "log"
    |
    */
    'drivers' => [
        'mailgun' => [
            'driver' => '\TypeRocket\Pro\Utility\Mailers\MailgunMailDriver',
            'region' => typerocket_env('TYPEROCKET_MAILGUN_REGION'),
            'api_key' => typerocket_env('TYPEROCKET_MAILGUN_API_KEY'),
            'domain' => typerocket_env('TYPEROCKET_MAILGUN_DOMAIN'),
            'from_override' => typerocket_env('TYPEROCKET_MAILGUN_FROM_OVERRIDE', false),
            'from_address' => typerocket_env('TYPEROCKET_MAILGUN_FROM_ADDRESS'),
            'from_name' => typerocket_env('TYPEROCKET_MAILGUN_FROM_NAME'),
        ],

        'wp' => [
            'driver' => '\TypeRocket\Pro\Utility\Mailers\WordPressMailDriver',
        ],

        'log' => [
            'driver' => '\TypeRocket\Pro\Utility\Mailers\LogMailDriver',
        ],
    ],
];