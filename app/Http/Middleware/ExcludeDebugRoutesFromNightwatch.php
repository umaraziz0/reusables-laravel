<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\AdminTool;
use Closure;
use Illuminate\Http\Request;
use Laravel\Nightwatch\Facades\Nightwatch;
use Symfony\Component\HttpFoundation\Response;

class ExcludeDebugRoutesFromNightwatch
{
    /**
     * Stop Nightwatch from sampling admin tool requests.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (AdminTool::forRequest($request) instanceof AdminTool) {
            Nightwatch::dontSample();
        }

        return $next($request);
    }
}
