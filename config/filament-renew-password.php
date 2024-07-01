<?php

return [
    'renew_password_days_period' => env('FILAMENT_RENEW_PASSWORD_DAYS_PERIOD', 90),

    'renew_password_timestamp_column' => env('FILAMENT_RENEW_PASSWORD_TIMESTAMP_COLUMN', 'last_renew_password_at'),

    'renew_password_force_column' => env('FILAMENT_RENEW_PASSWORD_FORCE_COLUMN', 'force_renew_password'),
];