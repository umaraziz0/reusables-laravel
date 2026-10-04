<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\Logging\RecordActivity;
use App\Actions\Logging\RedactSensitiveData;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Config;

class LogActivity implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param  array<string, mixed>  $withProperties
     * @param  array{
     *     attributes?: array<string, mixed>,
     *     old?: array<string, mixed>,
     * }  $attributeChanges
     */
    public function __construct(
        public string $module,
        public string $action,
        public string $message,
        public ?Model $performedOn,
        public ?Model $causedBy,
        public array $withProperties = [],
        public array $attributeChanges = [],
    ) {
        $this->withProperties   = RedactSensitiveData::redactArray($withProperties, $performedOn);
        $this->attributeChanges = RedactSensitiveData::redactChanges($attributeChanges, $performedOn);

        $queueName = Config::get('queue.queues.logs_activity');

        $this->onQueue(is_string($queueName) ? $queueName : 'default');
    }

    /**
     * Execute the job.
     */
    public function handle(RecordActivity $recordActivity): void
    {
        $recordActivity->handle(
            module: $this->module,
            action: $this->action,
            message: $this->message,
            performedOn: $this->performedOn,
            causedBy: $this->causedBy,
            withProperties: $this->withProperties,
            attributeChanges: $this->attributeChanges,
        );
    }
}
