<?php

declare(strict_types=1);

namespace App\Actions\Logging;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use JsonSerializable;
use stdClass;
use Traversable;

class RedactSensitiveData
{
    private const string REDACTED = '[REDACTED]';

    private const array SENSITIVE_KEYS = [
        'api_key',
        'private_key',
        'two_factor_recovery_codes',
        'authorization',
        'cookie',
        'cookies',
        'csrf',
        'captcha',
        'credentials',
    ];

    /**
     * Redact sensitive values from an array while preserving its structure.
     *
     * @param  array<array-key, mixed>  $data
     * @return array<array-key, mixed>
     */
    public static function redactArray(array $data, ?Model $performedOn = null): array
    {
        $deniedKeys = self::deniedKeys($performedOn);

        return self::redactMap($data, $deniedKeys);
    }

    /**
     * @param  array<array-key, mixed>  $attributeChanges
     * @return array{attributes?: array<array-key, mixed>, old?: array<array-key, mixed>}
     */
    public static function redactChanges(array $attributeChanges, ?Model $performedOn = null): array
    {
        $redactedChanges = [];

        foreach (['attributes', 'old'] as $key) {
            if (! is_array($attributeChanges[$key] ?? null) || $attributeChanges[$key] === []) {
                continue;
            }

            $redactedChanges[$key] = self::redactArray($attributeChanges[$key], $performedOn);
        }

        return $redactedChanges;
    }

    /**
     * Mask a person's name, keeping the first character of each word.
     *
     * @param  string  $name  e.g. "John Doe"
     * @return string e.g. "J*** D**"
     */
    public static function redactName(string $name): string
    {
        $name = mb_trim($name);

        if ($name === '') {
            return '';
        }

        $parts = preg_split('/\s+/u', $name) ?: [];

        return implode(' ', array_map(static function (string $part): string {
            $length = mb_strlen($part);

            return match (true) {
                $length <= 1 => '*',
                default      => mb_substr($part, 0, 1) . str_repeat('*', $length - 1),
            };
        }, $parts));
    }

    /**
     * Mask an email address, keeping the first and last character of the local
     * part and masking the first label of the domain while preserving the rest.
     *
     * @param  string  $email  e.g. "johndoe@mail.com"
     * @return string e.g. "j*****e@****.com"
     */
    public static function redactEmail(string $email): string
    {
        if ($email === '') {
            return '';
        }

        $atPosition = mb_strrpos($email, '@');

        if ($atPosition === false) {
            return str_repeat('*', mb_strlen($email));
        }

        $local  = mb_substr($email, 0, $atPosition);
        $domain = mb_substr($email, $atPosition + 1);

        if ($domain === '') {
            return str_repeat('*', mb_strlen($local)) . '@';
        }

        return self::maskLocalPart($local) . '@' . self::maskDomain($domain);
    }

    /**
     * Mask a phone number, keeping only the last four digits.
     *
     * @param  string  $phone  e.g. "081234567890"
     * @return string e.g. "********7890"
     */
    public static function redactPhone(string $phone): string
    {
        if (preg_match('/^\*+(?:\d{4})?$/', $phone) === 1) {
            return $phone;
        }

        $digits = preg_replace('/\D/', '', $phone) ?? '';

        if ($digits === '') {
            return '';
        }

        $length = mb_strlen($digits);

        if ($length < 4) {
            return str_repeat('*', $length);
        }

        return str_repeat('*', $length - 4) . mb_substr($digits, -4);
    }

    private static function maskLocalPart(string $local): string
    {
        $length = mb_strlen($local);

        return match (true) {
            $length <= 1  => '*',
            $length === 2 => mb_substr($local, 0, 1) . '*',
            default       => mb_substr($local, 0, 1) . str_repeat('*', $length - 2) . mb_substr($local, -1),
        };
    }

    private static function maskDomain(string $domain): string
    {
        if ($domain === '') {
            return '';
        }

        $firstDot = mb_strpos($domain, '.');

        if ($firstDot === false) {
            return str_repeat('*', mb_strlen($domain));
        }

        $firstLabel = mb_substr($domain, 0, $firstDot);
        $rest       = mb_substr($domain, $firstDot);

        return str_repeat('*', mb_strlen($firstLabel)) . $rest;
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @param  array<string, true>  $deniedKeys
     * @return array<array-key, mixed>
     */
    private static function redactMap(array $data, array $deniedKeys): array
    {
        foreach ($data as $key => $value) {
            $normalizedKey = is_string($key) ? self::normalizeKey($key) : null;

            if ($normalizedKey !== null && self::isSensitiveKey($normalizedKey, $deniedKeys)) {
                $data[$key] = self::REDACTED;

                continue;
            }

            if (is_string($value) && $normalizedKey !== null) {
                $data[$key] = self::redactPii($normalizedKey, $value);

                continue;
            }

            $data[$key] = self::redactValue($value, $deniedKeys);
        }

        return $data;
    }

    /**
     * @param  array<string, true>  $deniedKeys
     */
    private static function redactValue(mixed $value, array $deniedKeys): mixed
    {
        if (is_array($value)) {
            return self::redactMap($value, $deniedKeys);
        }

        if ($value instanceof Arrayable) {
            return self::redactMap($value->toArray(), $deniedKeys);
        }

        if ($value instanceof JsonSerializable) {
            return self::redactValue($value->jsonSerialize(), $deniedKeys);
        }

        if ($value instanceof Traversable) {
            return self::redactMap(iterator_to_array($value, true), $deniedKeys);
        }

        if ($value instanceof stdClass) {
            return self::redactMap(get_object_vars($value), $deniedKeys);
        }

        return $value;
    }

    /**
     * @param  array<string, true>  $deniedKeys
     */
    private static function isSensitiveKey(string $normalizedKey, array $deniedKeys): bool
    {
        return isset($deniedKeys[$normalizedKey])
            || preg_match('/(?:^|_)(?:password|token|secret)(?:_|$)/', $normalizedKey) === 1;
    }

    /**
     * @return array<string, true>
     */
    private static function deniedKeys(?Model $performedOn): array
    {
        $hiddenKeys = $performedOn?->getHidden() ?? [];

        return array_fill_keys(
            array_map(
                self::normalizeKey(...),
                [...self::SENSITIVE_KEYS, ...$hiddenKeys],
            ),
            true,
        );
    }

    private static function normalizeKey(string $key): string
    {
        $key = preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1_$2', $key) ?? $key;
        $key = preg_replace('/([a-z\d])([A-Z])/', '$1_$2', $key) ?? $key;
        $key = preg_replace('/[^a-zA-Z\d_]+/', '_', $key) ?? $key;

        return mb_strtolower($key);
    }

    private static function redactPii(string $normalizedKey, string $value): string
    {
        return match ($normalizedKey) {
            'name'                  => self::redactName($value),
            'email'                 => self::redactEmail($value),
            'phone', 'phone_number' => self::redactPhone($value),
            default                 => $value,
        };
    }
}
