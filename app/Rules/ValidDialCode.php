<?php

declare(strict_types=1);

namespace App\Rules;

use App\Data\CountryCodes;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Override;

class ValidDialCode implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    #[Override]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)
            || ! preg_match('/^\+[0-9]+$/', $value)
            || ! CountryCodes::isValidDialCode($value)) {
            $fail(__('validation.exists', ['attribute' => $attribute]));
        }
    }
}
