<?php
declare(strict_types=1);
return [
    'access_secret' => env('JWT_ACCESS_SECRET', 'replace_with_env_secret'),
    'refresh_secret' => env('JWT_REFRESH_SECRET', 'replace_with_env_secret'),
    'access_exp' => 3600,
    'refresh_exp' => 86400 * 30,
];
