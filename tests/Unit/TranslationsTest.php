<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;

const LANG_DIRECTORY = __DIR__ . '/../../lang';

/**
 * Dot-notation keys of a translation file. `attributes.*` holds locale-specific
 * field names, so it is left out of the comparison.
 *
 * @return list<string>
 */
function translationKeys(string $path): array
{
    return array_values(array_filter(
        array_keys(Arr::dot(require $path)),
        fn (string $key): bool => ! str_starts_with($key, 'attributes'),
    ));
}

test('translations load from the root lang directory', function (): void {
    // A `resources/lang` directory, if one exists, makes Laravel ignore `lang/`.
    expect(lang_path())->toBe(base_path('lang'))
        ->and(Lang::has('validation.captcha'))->toBeTrue();
});

test('the indonesian locale translates validation messages and json strings', function (): void {
    app()->setLocale('id');

    expect(__('validation.required', ['attribute' => 'nama']))->toBe('Kolom nama wajib diisi.')
        ->and(__('Profile updated.'))->toBe('Profil diperbarui.');
});

test('every english translation file has an indonesian counterpart with the same keys', function (string $file): void {
    $indonesianPath = LANG_DIRECTORY . "/id/{$file}";

    expect($indonesianPath)->toBeFile();

    $englishKeys    = translationKeys(LANG_DIRECTORY . "/en/{$file}");
    $indonesianKeys = translationKeys($indonesianPath);

    expect(array_diff($englishKeys, $indonesianKeys))->toBeEmpty('Missing in lang/id/' . $file)
        ->and(array_diff($indonesianKeys, $englishKeys))->toBeEmpty('Not in lang/en/' . $file);
})->with(fn (): array => array_map(basename(...), glob(LANG_DIRECTORY . '/en/*.php') ?: []));

test('indonesian json translations keep the placeholders of their english source string', function (): void {
    /** @var array<string, string> $translations */
    $translations = json_decode((string) file_get_contents(LANG_DIRECTORY . '/id.json'), true, flags: JSON_THROW_ON_ERROR);

    foreach ($translations as $source => $translation) {
        preg_match_all('/:\w+/', $source, $sourcePlaceholders);
        preg_match_all('/:\w+/', $translation, $translationPlaceholders);

        expect($translationPlaceholders[0])->toEqualCanonicalizing($sourcePlaceholders[0], $source);
    }
});
