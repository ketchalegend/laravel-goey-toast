<?php

declare(strict_types=1);

return [
    'session_key' => 'goey_toasts',

    'default_duration' => 4500,

    'max_visible' => 4,

    'position' => 'top-right',

    'z_index' => 9999,

    'dismissible' => true,

    'dedupe' => [
        'enabled' => true,
        'window_ms' => 3000,
    ],

    'animation' => [
        'spring_enabled' => true,
        'enter_duration' => 460,
        'leave_duration' => 230,
        'spring_curve' => 'cubic-bezier(0.175, 0.885, 0.32, 1.275)',
        'smooth_curve' => 'cubic-bezier(0.4, 0, 0.2, 1)',
        'start_offset' => 14,
        'start_scale' => 0.92,
    ],
];
