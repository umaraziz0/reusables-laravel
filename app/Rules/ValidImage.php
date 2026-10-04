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

class ValidImage implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    #[Override]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $mimes        = Config::array('media.image.mimes');
        $maxKilobytes = Config::integer('media.image.max_filesize');

        $validator = Validator::make(
            [$attribute => $value],
            // File::types not File::image() — Laravel image rule omits avif
            [$attribute => [
                File::types($mimes)->max($maxKilobytes),
            ]],
        );

        if ($validator->fails()) {
            $fail($validator->errors()->first($attribute));
        }
    }
}
