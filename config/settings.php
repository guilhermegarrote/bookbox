<?php

declare(strict_types=1);

return [
    'max_book_loans' => [
        'type' => 'integer',
        'min' => 1,
        'max' => 10,
        'default' => 3,
    ],
    'default_due_days' => [
        'type' => 'integer',
        'min' => 1,
        'max' => 180,
        'default' => 14,
    ],
    'extension_days' => [
        'type' => 'integer',
        'min' => 1,
        'max' => 180,
        'default' => 7,
    ],
];
