<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Translation\PotentiallyTranslatedString;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\ImageFile;
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
        $maxWidth     = Config::integer('media.image.max_width');
        $maxHeight    = Config::integer('media.image.max_height');

        $dimensions = Rule::dimensions()
            ->maxWidth($maxWidth)
            ->maxHeight($maxHeight);

        $validator = Validator::make(
            [$attribute => $value],
            [$attribute => [
                ImageFile::types($mimes)
                    ->max($maxKilobytes)
                    ->dimensions($dimensions),
            ]],
        );

        if ($validator->fails()) {
            $fail($validator->errors()->first($attribute));
        }
    }
}
