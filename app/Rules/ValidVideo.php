<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Translation\PotentiallyTranslatedString;
use Illuminate\Validation\Rules\File;
use Override;

class ValidVideo implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    #[Override]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $mimes        = Config::array('media.video.mimes');
        $maxKilobytes = Config::integer('media.video.max_filesize');

        $validator = Validator::make(
            [$attribute => $value],
            [$attribute => [
                File::types($mimes)->max($maxKilobytes),
            ]],
        );

        if ($validator->fails()) {
            $fail($validator->errors()->first($attribute));
        }
    }
}
