<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Actions\Logging\LogException;
use App\Actions\Logging\WriteLog;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class ValidateCaptcha
{
    public function __construct(
        private readonly WriteLog $writeLog,
        private readonly LogException $logException,
    ) {}

    public function handle(mixed $captcha, mixed $idempotencyKey): bool
    {
        if (! is_string($captcha) || $captcha === '') {
            return false;
        }

        $captchaUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

        $request = [
            'secret'   => Config::string('services.turnstile.secret_key'),
            'response' => $captcha,
        ];

        if ($idempotencyKey) {
            $request['idempotency_key'] = $idempotencyKey;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->connectTimeout(3)
                ->post($captchaUrl, $request)
                ->json();
        } catch (ConnectionException $exception) {
            $this->logException->handle(
                module: 'External',
                action: 'Validate Turnstile',
                message: 'connection failed',
                exception: $exception,
                additionalContext: [
                    'captchaUrl' => $captchaUrl,
                ],
            );

            return false;
        }

        if (! data_get($response, 'success')) {
            $this->writeLog->handle(
                level: 'error',
                module: 'External',
                action: 'Validate Turnstile',
                message: 'verification failed',
                context: [
                    'captchaUrl' => $captchaUrl,
                    'response'   => $response,
                ],
            );

            return false;
        }

        return true;
    }
}
