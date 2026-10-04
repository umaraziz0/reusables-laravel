<?php

declare(strict_types=1);

namespace App\Actions\Logging;

use Illuminate\Database\Eloquent\Model;

class RecordActivity
{
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
        $log = activity();

        if ($performedOn instanceof Model) {
            $log->performedOn($performedOn);
        }

        if ($causedBy instanceof Model) {
            $log->causedBy($causedBy);
        }

        $normalizedAttributeChanges = RedactSensitiveData::redactChanges($attributeChanges, $performedOn);

        if ($normalizedAttributeChanges !== []) {
            $log->withChanges($normalizedAttributeChanges);
        }

        $log->withProperties(RedactSensitiveData::redactArray($withProperties, $performedOn))
            ->event("{$module} - {$action}")
            ->log($message);
    }
}
