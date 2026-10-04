<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    |
    | The paths that should be accessible via CORS. These paths will be
    | checked against the request path to determine if CORS headers should
    | be applied.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    |
    | The HTTP methods that are allowed for cross-origin requests.
    |
    */

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | The origins that are allowed to make cross-origin requests. Use ['*']
    | for development only. In production, specify exact origins or use
    | allowed_origins_patterns for dynamic matching.
    |
    | The APP_URL is automatically included in the allowed origins.
    |
    */

    'allowed_origins' => (function (): array {
        $appUrl     = config('app.url');
        $envOrigins = (string) env('CORS_ALLOWED_ORIGINS', '*');

        // If origins are not specified, return the app URL
        if ($envOrigins === '') {
            return [$appUrl];
        }

        // If wildcard is specified, return it (for development)
        if ($envOrigins === '*') {
            return ['*'];
        }

        // Parse environment origins
        $origins = array_filter(array_map(trim(...), explode(',', $envOrigins)));

        // Add APP_URL if it's not already in the list
        if ($appUrl && ! in_array($appUrl, $origins, true)) {
            $origins[] = $appUrl;
        }

        return array_values(array_filter($origins));
    })(),

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins Patterns
    |--------------------------------------------------------------------------
    |
    | Patterns that can be used to match allowed origins dynamically.
    | Useful for matching subdomains or multiple domains.
    |
    | Example: ['/^https?:\/\/.*\.example\.com$/']
    |
    */

    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | The headers that are allowed to be sent with cross-origin requests.
    | Common headers include: Content-Type, Authorization, X-Requested-With,
    | X-CSRF-TOKEN, Accept, Origin.
    |
    */

    'allowed_headers' => [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'X-CSRF-TOKEN',
        'X-XSRF-TOKEN',
        'Accept',
        'Origin',
        'Precognition',
        'Precognition-Validate-Only',
    ],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Headers that are exposed to the client in cross-origin responses.
    | Only include headers that the client needs to access.
    |
    */

    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | The maximum time (in seconds) that a browser can cache the preflight
    | response. Set to 0 to disable caching. Recommended: 86400 (24 hours)
    | for production to reduce preflight requests.
    |
    */

    'max_age' => (int) env('CORS_MAX_AGE', 86400),

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Whether to allow credentials (cookies, authorization headers) to be
    | sent with cross-origin requests. Set to true only if you need to send
    | cookies or authorization headers from a different origin.
    |
    | When true, you cannot use ['*'] for allowed_origins - you must specify
    | exact origins.
    |
    */

    'supports_credentials' => (bool) env('CORS_SUPPORTS_CREDENTIALS', false),

];
