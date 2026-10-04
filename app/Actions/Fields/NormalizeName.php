<?php

declare(strict_types=1);

namespace App\Actions\Fields;

use Illuminate\Support\Str;

class NormalizeName
{
    /**
     * Normalize the name by trimming leading and trailing whitespace.
     * e.g "  Jean   Luc  " → "Jean Luc"
     */
    public function handle(string $value): string
    {
        return Str::of($value)->squish()->toString();
    }
}
