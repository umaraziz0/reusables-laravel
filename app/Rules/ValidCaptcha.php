<?php

declare(strict_types=1);

namespace App\Rules;

use App\Actions\Auth\ValidateCaptcha;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidCaptcha implements ValidationRule
{
    public bool $implicit = true;

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=):PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isValid = resolve(ValidateCaptcha::class)->handle($value, request()->input('idempotency_key'));

        if (! $isValid) {
            $fail(__('validation.captcha'));

            return;
        }
    }
}
