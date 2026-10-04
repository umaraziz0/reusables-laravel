<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Actions\Logging\LogSecurity;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class IpWhitelisted
{
    public function __construct(
        private readonly LogSecurity $logSecurity,
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (SymfonyResponse)  $next
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        if (app()->isLocal()) {
            return $next($request);
        }

        $allowedIps = Config::array('api.allowed_ips', []);
        $clientIp   = $request->ip();

        if ($allowedIps === [] || ! $this->isIpAllowed($clientIp, $allowedIps)) {
            $this->logSecurity->handle(
                module: 'API',
                action: 'IP Denied',
                message: 'ip denied',
                data: [
                    'ip'                 => $clientIp,
                    'path'               => $request->path(),
                    'method'             => $request->method(),
                    'is_allowed_ips_set' => $allowedIps !== [],
                ],
            );

            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
                'data'    => null,
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }

    /**
     * @param  array<int, string>  $allowedIps
     */
    private function isIpAllowed(?string $clientIp, array $allowedIps): bool
    {
        if ($clientIp === null) {
            return false;
        }

        return array_any($allowedIps, fn (string|array $allowedIp): bool => IpUtils::checkIp($clientIp, $allowedIp));
    }
}
