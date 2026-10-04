<?php

declare(strict_types=1);

namespace App\Actions\Logging;

use App\Jobs\LogActivity as LogActivityJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Throwable;

class LogActivity
{
    public function __construct(
        private readonly RecordActivity $recordActivity,
    ) {}

    /**
     * @param  array<string, mixed>  $withProperties
     * @param  array{
     *     attributes?: array<string, mixed>,
     *     old?: array<string, mixed>,
     * }  $attributeChanges
     */
    public function handle(
        string $module,
        string $action,
        string $message,
        ?Model $performedOn = null,
        ?Model $causedBy = null,
        array $withProperties = [],
        array $attributeChanges = [],
    ): void {
        $causedBy ??= Auth::user();

        if (! app()->isLocal()) {
            dispatch(new LogActivityJob(
                module: $module,
                action: $action,
                message: $message,
                performedOn: $performedOn,
                causedBy: $causedBy,
                withProperties: $withProperties,
                attributeChanges: $attributeChanges,
            ));

            return;
        }

        try {
            $this->recordActivity->handle(
                module: $module,
                action: $action,
                message: $message,
                performedOn: $performedOn,
                causedBy: $causedBy,
                withProperties: $withProperties,
                attributeChanges: $attributeChanges,
            );
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
