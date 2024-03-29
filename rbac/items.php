<?php
return [
    'signup' => [
        'type' => 2,
    ],
    'login' => [
        'type' => 2,
    ],
    'logout' => [
        'type' => 2,
    ],
    'profile' => [
        'type' => 2,
    ],
    'error' => [
        'type' => 2,
    ],
    'index' => [
        'type' => 2,
    ],
    'view' => [
        'type' => 2,
    ],
    'create' => [
        'type' => 2,
    ],
    'update' => [
        'type' => 2,
    ],
    'delete' => [
        'type' => 2,
    ],
    'close' => [
        'type' => 2,
    ],
    'about' => [
        'type' => 2,
    ],
    'contact' => [
        'type' => 2,
    ],
    'category' => [
        'type' => 2,
    ],
    'subject' => [
        'type' => 2,
    ],
    'post' => [
        'type' => 2,
    ],
    'guest' => [
        'type' => 1,
        'ruleName' => 'userRole',
        'children' => [
            'index',
            'login',
            'logout',
            'error',
            'signup',
            'category',
            'subject',
            'post',
        ],
    ],
    'user' => [
        'type' => 1,
        'ruleName' => 'userRole',
        'children' => [
            'create',
            'update',
            'profile',
        ],
    ],
    'admin' => [
        'type' => 1,
        'ruleName' => 'userRole',
        'children' => [
            'about',
            'close',
            'delete',
            'user',
        ],
    ],
];
