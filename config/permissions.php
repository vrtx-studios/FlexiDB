<?php
return [
    'abilities' => [
        'administrator' => [
            'system-debug' => true,
            'user-create' => true,
            'user-update' => true,
            'user-delete' => true,
            'inherits' => ['user']
        ],
        'user' => [
            'table-create' => true,
            'table-delete' => true,
            'data-create' => true,
            'data-update' => true,
            'data-delete' => true,
            'inherits' => []
        ]
    ]
];
