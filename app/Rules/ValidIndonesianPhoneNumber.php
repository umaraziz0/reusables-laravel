<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Override;

class ValidIndonesianPhoneNumber implements ValidationRule
{
    private const int MIN_LENGTH = 9;

    private const int MAX_LENGTH = 12;

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    #[Override]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $attributeName = __('validation.attributes.phone_number');

        if (! is_string($value)) {
            $fail(__('validation.numeric', ['attribute' => $attributeName]));

            return;
        }

        if (preg_match('/[^\p{L}\p{N}\s]/u', $value)) {
            $fail(__('validation.no_special_characters', ['attribute' => $attributeName]));

            return;
        }

        if (preg_match('/\s/', $value)) {
            $fail(__('validation.no_spaces', ['attribute' => $attributeName]));

            return;
        }

        if (! preg_match('/^[0-9]+$/', $value)) {
            $fail(__('validation.numeric', ['attribute' => $attributeName]));

            return;
        }

        if (str_starts_with($value, '0')) {
            $fail(__('validation.no_leading_zeroes', ['attribute' => $attributeName]));

            return;
        }

        if (mb_strlen($value) < self::MIN_LENGTH) {
            $fail(__('validation.min.string', ['attribute' => $attributeName, 'min' => self::MIN_LENGTH]));

            return;
        }

        if (mb_strlen($value) > self::MAX_LENGTH) {
            $fail(__('validation.max.string', ['attribute' => $attributeName, 'max' => self::MAX_LENGTH]));
        }
    }
}
