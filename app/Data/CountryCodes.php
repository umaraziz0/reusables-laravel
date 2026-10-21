<?php

declare(strict_types=1);

namespace App\Data;

final class CountryCodes
{
    /**
     * Shared with the frontend, which imports the same file.
     */
    private const string PATH = 'data/country-codes.json';

    /**
     * Read once per process. The list never changes at runtime, so keeping it
     * across Octane requests is safe.
     *
     * @var list<array{name: string, dial_code: string, code: string}>|null
     */
    private static ?array $countries = null;

    /**
     * @return list<array{name: string, dial_code: string, code: string}>
     */
    public static function all(): array
    {
        if (self::$countries === null) {
            /** @var list<array{name: string, dial_code: string, code: string}> $countries */
            $countries = json_decode(
                (string) file_get_contents(resource_path(self::PATH)),
                associative: true,
                flags: JSON_THROW_ON_ERROR,
            );

            self::$countries = $countries;
        }

        return self::$countries;
    }

    /**
     * @return list<string>
     */
    public static function dialCodes(): array
    {
        return array_values(array_unique(array_column(self::all(), 'dial_code')));
    }

    public static function isValidDialCode(string $dialCode): bool
    {
        return in_array($dialCode, self::dialCodes(), true);
    }
}
