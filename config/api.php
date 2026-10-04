<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Private API IP Whitelist
    |--------------------------------------------------------------------------
    |
    | Comma-separated list of IP addresses and CIDR ranges allowed to access
    | private API routes (those behind auth:sanctum and ip_whitelist middleware).
    | In non-local environments, an empty list denies all private API requests.
    |
    | Example: 203.0.113.10,10.0.0.0/8
    |
    | When behind a load balancer, configure trusted proxies in bootstrap/app.php
    | so $request->ip() reflects the real client IP.
    |
    */

    'allowed_ips' => array_values(array_filter(array_map(
        trim(...),
        explode(',', (string) env('API_ALLOWED_IPS', '')),
    ))),

];
