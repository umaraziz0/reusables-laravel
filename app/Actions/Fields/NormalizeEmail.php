<?php

declare(strict_types=1);

namespace App\Actions\Fields;

use Illuminate\Support\Str;

class NormalizeEmail
{
    /**
     * Normalize the email by converting it to lowercase and trimming leading and trailing whitespace.
     * e.g "  jean.luc@example.com  " → "jean.luc@example.com"
     */
    public function handle(string $value): string
    {
        return Str::lower(mb_trim($value));
    }
}
