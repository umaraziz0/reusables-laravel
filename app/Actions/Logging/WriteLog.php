<?php

declare(strict_types=1);

namespace App\Actions\Logging;

use App\Actions\Logging\Concerns\GathersLogContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WriteLog
{
    use GathersLogContext;

    /**
     * @param  array<string, mixed>  $context
     */
    public function handle(
        string $level,
        string $module,
        string $action,
        string $message,
        array $context = [],
        ?Request $request = null,
    ): void {
        Log::log($level, "{$module} - {$action} - {$message}", $this->getGlobalContext($request, $context));
    }
}
