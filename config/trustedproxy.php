<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Cloudflare IP Ranges
|--------------------------------------------------------------------------
|
| The ranges the "cloudflare" token expands to. Cloudflare publishes the
| current list at https://www.cloudflare.com/ips/ (fetched 2026-11-26).
|
*/

$cloudflareRanges = [
    '173.245.48.0/20',
    '103.21.244.0/22',
    '103.22.200.0/22',
    '103.31.4.0/22',
    '141.101.64.0/18',
    '108.162.192.0/18',
    '190.93.240.0/20',
    '188.114.96.0/20',
    '197.234.240.0/22',
    '198.41.128.0/17',
    '162.158.0.0/15',
    '104.16.0.0/13',
    '104.24.0.0/14',
    '172.64.0.0/13',
    '131.0.72.0/22',
    '2400:cb00::/32',
    '2606:4700::/32',
    '2803:f800::/32',
    '2405:b500::/32',
    '2405:8100::/32',
    '2a06:98c0::/29',
    '2c0f:f248::/32',
];

$configuredProxies = array_values(array_filter(array_map(
    trim(...),
    explode(',', (string) env('TRUSTED_PROXIES', '')),
)));

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Comma-separated list of proxies whose X-Forwarded-For and
    | X-Forwarded-Proto headers are trusted (the header set is configured in
    | bootstrap/app.php). Leave empty when clients connect directly, so
    | forwarded headers are ignored. Use the "cloudflare" token when traffic
    | is proxied through Cloudflare. Add 127.0.0.1 when a local reverse proxy
    | (such as nginx in front of Octane) forwards requests. There is no
    | wildcard: "*" is treated as a literal entry and trusts nobody.
    |
    | Laravel's TrustProxies middleware reads this key on each request.
    |
    | The "cloudflare" token trusts every request from Cloudflare's ranges,
    | not only your zone's proxy (other customers' Workers, WARP users). Such
    | a request can set X-Forwarded-For to any address, including one in
    | API_ALLOWED_IPS. Restrict the origin to your zone with Authenticated
    | Origin Pulls, or use Cloudflare Tunnel with 127.0.0.1 instead.
    |
    | Example: cloudflare,127.0.0.1
    |
    */

    'proxies' => array_merge(...array_map(
        fn (string $proxy): array => $proxy === 'cloudflare' ? $cloudflareRanges : [$proxy],
        $configuredProxies,
    )),

];
