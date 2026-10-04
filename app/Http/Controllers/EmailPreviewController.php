<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\FailedJobNotification;
use Illuminate\Http\Response;

class EmailPreviewController extends Controller
{
    public function failedJobEmail(): Response
    {
        $mailable = new FailedJobNotification(
            connectionName: 'redis',
            failedQueue: 'default',
            uuid: 'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
            jobName: 'App\Jobs\ProcessOrder',
            exceptionClass: 'RuntimeException',
            exceptionMessage: 'Order #12345 could not be processed: inventory check failed.',
            stackTrace: "#0 /app/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(123): handle()\n"
                . "#1 /app/vendor/laravel/framework/src/Illuminate/Queue/Jobs/RedisJob.php(90): process()\n"
                . "#2 /app/app/Jobs/ProcessOrder.php(45): handle()\n"
                . "#3 /app/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(124): Illuminate\\Queue\\CallQueuedHandler::call()\n"
                . "#4 /app/vendor/laravel/framework/src/Illuminate/Queue/Jobs/RedisJob.php(98): process()\n"
                . '... (truncated for preview)',
            failedAt: now()->toIso8601String(),
            attempts: 3,
        );

        return response($mailable->render(), 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }
}
