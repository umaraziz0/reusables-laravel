<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Hide the component preview routes when `app.component_previews` is off.
 * Their pages are left out of the frontend build then, so they cannot render.
 */
class EnsureComponentPreviewsEnabled
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(Config::boolean('app.component_previews'), Response::HTTP_NOT_FOUND);

        return $next($request);
    }
}
