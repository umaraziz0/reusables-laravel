<?php

declare(strict_types=1);

namespace App\Actions\Logging\Concerns;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait GathersLogContext
{
    /**
     * @param  array<string, mixed>  $additionalContext
     * @return array<string, mixed>
     */
    protected function getGlobalContext(?Request $request, array $additionalContext = []): array
    {
        $request ??= request();

        $context = [
            'timestamp'  => now()->toISOString(),
            'ip_address' => $this->getClientIpAddress($request),
            'user_agent' => $request->userAgent(),
            'url'        => $request->fullUrl(),
            'method'     => $request->method(),
            'session_id' => $this->getSessionId($request),
        ];

        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            $context['auth_user_uuid'] = $user->uuid;
        }

        $context['headers'] = [
            'accept'          => $request->header('Accept'),
            'content_type'    => $request->header('Content-Type'),
            'referer'         => $request->header('Referer'),
            'x_forwarded_for' => $request->header('X-Forwarded-For'),
            'x_real_ip'       => $request->header('X-Real-IP'),
        ];

        return array_merge($context, $additionalContext);
    }

    /**
     * Stateless requests have no session ID to include in log context.
     */
    protected function getSessionId(Request $request): ?string
    {
        if (! $request->hasSession()) {
            return null;
        }

        return $request->session()->getId();
    }

    protected function getClientIpAddress(Request $request): ?string
    {
        return $request->ip();
    }
}
