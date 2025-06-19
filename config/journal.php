<?php
return [
    /**
     * 日志渠道
     */
    "channels" => [],

    /**
     * 日志每页显示行数
     */
    "perPage" => 50,

    /**
     * 登录用户设置
     */
    "users" => [
        [
            'name' => env('JOURNAL_USERS_NAME', 'admin'),
            'email' => env('JOURNAL_USERS_EMAIL', 'admin@admin.com'),
            'password' => env('JOURNAL_USERS_PASSWORD', 'admin123456'),
        ],
    ],

];
