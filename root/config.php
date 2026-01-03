<?php

$env = parse_ini_file(__DIR__ . '/.env', false, INI_SCANNER_TYPED);

return [
    'smtp' => [
        'host' => $env['SMTP_HOST'],
        'port' => $env['SMTP_PORT'],
        'username' => $env['SMTP_USER'],
        'password' => $env['SMTP_PASS'],
        'secure' => 'ssl'
    ],
    'mail' => [
        'to' => $env['MAIL_TO'],
        'subject' => $env['MAIL_SUBJECT']
    ]
];
