<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\AdminTool;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Base Content Security Policy directives.
     * These can be easily modified for easier configuration.
     *
     * @var array<string, array<string>>
     */
    private array $baseCsp = [
        'default-src' => ["'self'"],
        'script-src'  => [
            "'self'",
            'https://challenges.cloudflare.com/turnstile/v0/api.js',
        ],
        'style-src' => [
            "'self'",
            "'unsafe-inline'", // Vue :style bindings + nonce-less inline styles; nonce is not applied to style-src
            'https://fonts.gstatic.com',
            'https://fonts.googleapis.com',
            'https://fonts.bunny.net',
            'https://rsms.me', // Inter font CDN (default Laravel)
        ],
        'img-src' => [
            "'self'",
            'data:',
            'blob:',
        ],
        'font-src' => [
            "'self'",
            'data:',
            'https://fonts.gstatic.com',
            'https://fonts.googleapis.com',
            'https://fonts.bunny.net',
            'https://rsms.me', // Inter font files (default Laravel)
        ],
        'connect-src' => [
            "'self'",
            // Iconify API hosts (re-enable if @iconify/vue is added back)
            // 'https://api.unisvg.com',
            // 'https://api.iconify.design',
            // 'https://api.simplesvg.com',
        ],
        'media-src' => [
            "'self'",
        ],
        'frame-src' => [
            "'self'",
            'https://challenges.cloudflare.com',
        ],
        'object-src' => [
            "'none'",
        ],
        'base-uri' => [
            "'self'",
        ],
        'form-action' => [
            "'self'",
        ],
        'frame-ancestors' => [
            "'none'",
        ],
    ];

    /**
     * Build the Content Security Policy header.
     */
    protected function buildContentSecurityPolicy(Request $request, string $nonce): string
    {
        $isDevelopment = app()->environment(['local', 'development']);

        // Start with base CSP configuration
        $csp = $this->baseCsp;

        // Add nonce to script-src only.
        // We intentionally do NOT add a nonce to style-src: Vue components emit inline
        // styles via :style bindings, and a nonce would cause the 'unsafe-inline' in the
        // base style-src to be ignored, breaking them. 'unsafe-inline' stays in both dev and prod.
        $csp['script-src'][] = "'nonce-{$nonce}'";

        // Add development-specific sources
        if ($isDevelopment) {
            $vitePort = 5173; // Vite default port

            // Add Vite dev server to script-src
            $csp['script-src'][] = "http://localhost:{$vitePort}";
            $csp['script-src'][] = "http://127.0.0.1:{$vitePort}";
            $csp['script-src'][] = "'unsafe-eval'"; // Needed for Vite HMR

            // Add Vite dev server to style-src
            $csp['style-src'][] = "http://localhost:{$vitePort}";
            $csp['style-src'][] = "http://127.0.0.1:{$vitePort}";

            // Add Vite dev server to connect-src (for HMR WebSocket)
            $csp['connect-src'][] = "ws://localhost:{$vitePort}";
            $csp['connect-src'][] = "ws://127.0.0.1:{$vitePort}";
            $csp['connect-src'][] = "http://localhost:{$vitePort}";
            $csp['connect-src'][] = "http://127.0.0.1:{$vitePort}";
        }

        // Build CSP directive strings
        $directives = [];

        foreach ($csp as $directive => $sources) {
            $directives[] = "{$directive} " . implode(' ', $sources);
        }

        // Add upgrade-insecure-requests in production
        if (! $isDevelopment) {
            $directives[] = 'upgrade-insecure-requests';
        }

        return implode('; ', $directives);
    }

    /**
     * Build the Permissions-Policy header.
     */
    protected function buildPermissionsPolicy(): string
    {
        $policies = [
            'accelerometer'                   => [],
            'ambient-light-sensor'            => [],
            'autoplay'                        => [],
            'battery'                         => [],
            'camera'                          => [],
            'cross-origin-isolated'           => [],
            'display-capture'                 => [],
            'document-domain'                 => [],
            'encrypted-media'                 => [],
            'execution-while-not-rendered'    => [],
            'execution-while-out-of-viewport' => [],
            'fullscreen'                      => ['self'],
            'geolocation'                     => [],
            'gyroscope'                       => [],
            'keyboard-map'                    => [],
            'magnetometer'                    => [],
            'microphone'                      => [],
            'midi'                            => [],
            'navigation-override'             => [],
            'payment'                         => [],
            'picture-in-picture'              => [],
            'publickey-credentials-get'       => [],
            'screen-wake-lock'                => [],
            'sync-xhr'                        => [],
            'usb'                             => [],
            'web-share'                       => [],
            'xr-spatial-tracking'             => [],
        ];

        $policyParts = [];

        foreach ($policies as $feature => $allowlist) {
            if ($allowlist === []) {
                // Deny all: feature=()
                $policyParts[] = "{$feature}=()";
            } else {
                // Allow specific origins: feature=(origin1 origin2)
                $origins       = implode(' ', $allowlist);
                $policyParts[] = "{$feature}=({$origins})";
            }
        }

        return implode(', ', $policyParts);
    }

    /**
     * Determine if Content-Security-Policy should be skipped for the current request.
     * JSON API responses have no document to protect, and admin tools need inline scripts/eval
     * that strict CSP blocks. All other security headers are still applied for defense in depth.
     */
    protected function shouldSkipCsp(Request $request): bool
    {
        return $request->is('api', 'api/*') || AdminTool::forRequest($request) instanceof AdminTool;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Generate CSP nonce for Vite assets (needed even if headers are skipped)
        Vite::useCspNonce();
        $nonce = Vite::cspNonce();

        // Share nonce with Blade templates for inline scripts and styles
        View::share('cspNonce', $nonce);

        $response = $next($request);

        $headers = [];

        // Content Security Policy (skipped for dashboards that need inline scripts/eval)
        if (! $this->shouldSkipCsp($request)) {
            $headers['Content-Security-Policy'] = $this->buildContentSecurityPolicy($request, (string) $nonce);
        }

        // X-Content-Type-Options: Prevent MIME type sniffing
        $headers['X-Content-Type-Options'] = 'nosniff';

        // X-Frame-Options: Prevent clickjacking
        $headers['X-Frame-Options'] = 'DENY';

        // X-XSS-Protection: Disable XSS filter (deprecated but still used by older browsers)
        $headers['X-XSS-Protection'] = '0';

        // Referrer-Policy: Control referrer information
        $headers['Referrer-Policy'] = 'strict-origin-when-cross-origin';

        // Permissions-Policy: Restrict browser features
        $headers['Permissions-Policy'] = $this->buildPermissionsPolicy();

        // Strict-Transport-Security: Force HTTPS (only in production with HTTPS)
        if ($request->secure() && app()->isProduction()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains; preload';
        }

        // Apply security headers without overriding CORS headers set by HandleCors middleware
        // CORS headers (Access-Control-*) are set by Laravel's HandleCors middleware
        // which runs in the global middleware stack before this middleware
        foreach ($headers as $key => $value) {
            // Only set header if it's not a CORS header (to avoid conflicts)
            // CORS headers are handled by Laravel's HandleCors middleware
            if (! str_starts_with($key, 'Access-Control-')) {
                // Replace so our security policy is authoritative over any upstream value.
                $response->headers->set($key, $value, true);
            }
        }

        return $response;
    }
}
