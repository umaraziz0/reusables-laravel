<?php

declare(strict_types=1);

use App\Actions\Logging\RedactSensitiveData;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;

dataset('names', [
    'full name'          => ['John Doe', 'J*** D**'],
    'single word'        => ['Madonna', 'M******'],
    'three words'        => ['John Ronald Tolkien', 'J*** R***** T******'],
    'empty'              => ['', ''],
    'single character'   => ['A', '*'],
    'two character word' => ['Jo', 'J*'],
    'extra whitespace'   => ['John   Doe', 'J*** D**'],
    'leading whitespace' => [' John Doe', 'J*** D**'],
    'unicode name'       => ['José García', 'J*** G*****'],
    'multibyte first'    => ['Élodie Smith', 'É***** S****'],
]);

dataset('emails', [
    'standard'           => ['johndoe@mail.com', 'j*****e@****.com'],
    'short local part'   => ['ab@mail.com', 'a*@****.com'],
    'single char local'  => ['a@mail.com', '*@****.com'],
    'multi label domain' => ['user@sub.mail.co.uk', 'u**r@***.mail.co.uk'],
    'empty'              => ['', ''],
    'no at sign'         => ['notanemail', '**********'],
    'at only'            => ['@mail.com', '*@****.com'],
    'local only'         => ['johndoe@', '*******@'],
    'two char local'     => ['ab@x.io', 'a*@*.io'],
    'multibyte local'    => ['éa@mail.com', 'é*@****.com'],
]);

dataset('phones', [
    'standard'        => ['081234567890', '********7890'],
    'with dashes'     => ['081-234-567890', '********7890'],
    'with spaces'     => ['081 234 567890', '********7890'],
    'with plus'       => ['+6281234567890', '*********7890'],
    'short'           => ['123', '***'],
    'exactly four'    => ['1234', '1234'],
    'five digits'     => ['12345', '*2345'],
    'empty'           => ['', ''],
    'non digits only' => ['--++', ''],
]);

test('redacts names', function (string $input, string $expected): void {
    expect(RedactSensitiveData::redactName($input))->toBe($expected);
})->with('names');

test('redacts emails', function (string $input, string $expected): void {
    expect(RedactSensitiveData::redactEmail($input))->toBe($expected);
})->with('emails');

test('redacts phone numbers', function (string $input, string $expected): void {
    expect(RedactSensitiveData::redactPhone($input))->toBe($expected);
})->with('phones');

test('redacts phone numbers idempotently', function (): void {
    foreach (['081234567890', '081-234-567890', '123', '', '--++'] as $phone) {
        $redacted = RedactSensitiveData::redactPhone($phone);

        expect(RedactSensitiveData::redactPhone($redacted))->toBe($redacted);
    }
});

test('redacts sensitive data recursively without changing input or model', function (): void {
    $performedOn = new #[Hidden(['internalCode'])]
    class extends Model {};
    $data = [
        'password'               => 'password',
        'passwordConfirmation'   => 'confirmation',
        'rememberToken'          => 'remember',
        'accessToken'            => 'access',
        'api_token'              => 'api',
        'password_hash'          => 'password hash',
        'user_password'          => 'user password',
        'secret_key'             => 'secret key',
        'api_key'                => 'api key',
        'private_key'            => 'private key',
        'credentials'            => 'credentials',
        'userToken'              => 'user token',
        'clientSecret'           => 'client secret',
        'twoFactorRecoveryCodes' => ['one', 'two'],
        'authorization'          => 'Bearer token',
        'cookies'                => 'cookie data',
        'CSRF'                   => 'csrf',
        '_token'                 => 'token',
        'captcha'                => 'captcha',
        'internalCode'           => 'internal',
        'secretary'              => 'safe',
        'name'                   => 'John Doe',
        'email'                  => 'johndoe@mail.com',
        'phoneNumber'            => '081234567890',
        'nullable'               => null,
        'nested'                 => [
            'custom_secret' => 'nested secret',
            'secretary'     => 'safe',
            'email'         => 123,
            'list'          => [['password' => 'nested password']],
        ],
    ];
    $original = $data;

    $redacted = RedactSensitiveData::redactArray($data, $performedOn);

    expect($redacted)->toMatchArray([
        'password'               => '[REDACTED]',
        'passwordConfirmation'   => '[REDACTED]',
        'rememberToken'          => '[REDACTED]',
        'accessToken'            => '[REDACTED]',
        'api_token'              => '[REDACTED]',
        'password_hash'          => '[REDACTED]',
        'user_password'          => '[REDACTED]',
        'secret_key'             => '[REDACTED]',
        'api_key'                => '[REDACTED]',
        'private_key'            => '[REDACTED]',
        'credentials'            => '[REDACTED]',
        'userToken'              => '[REDACTED]',
        'clientSecret'           => '[REDACTED]',
        'twoFactorRecoveryCodes' => '[REDACTED]',
        'authorization'          => '[REDACTED]',
        'cookies'                => '[REDACTED]',
        'CSRF'                   => '[REDACTED]',
        '_token'                 => '[REDACTED]',
        'captcha'                => '[REDACTED]',
        'internalCode'           => '[REDACTED]',
        'secretary'              => 'safe',
        'name'                   => 'J*** D**',
        'email'                  => 'j*****e@****.com',
        'phoneNumber'            => '********7890',
        'nullable'               => null,
        'nested'                 => [
            'custom_secret' => '[REDACTED]',
            'secretary'     => 'safe',
            'email'         => 123,
            'list'          => [['password' => '[REDACTED]']],
        ],
    ])->and($data)->toBe($original)
        ->and($performedOn->getHidden())->toBe(['internalCode']);
});

test('redacts nested common JSON containers', function (): void {
    $redacted = RedactSensitiveData::redactArray([
        'collection' => collect(['password' => 'collection secret']),
        'arrayable'  => new class implements Arrayable
        {
            /**
             * @return array<string, string>
             */
            public function toArray(): array
            {
                return ['user_password' => 'arrayable secret'];
            }
        },
        'json' => new class implements JsonSerializable
        {
            /**
             * @return array<string, string>
             */
            public function jsonSerialize(): array
            {
                return ['secret_key' => 'json secret'];
            }
        },
        'traversable' => new ArrayIterator(['api_key' => 'iterator secret']),
        'object'      => (object) ['private_key' => 'object secret'],
    ]);

    expect($redacted)->toBe([
        'collection'  => ['password' => '[REDACTED]'],
        'arrayable'   => ['user_password' => '[REDACTED]'],
        'json'        => ['secret_key' => '[REDACTED]'],
        'traversable' => ['api_key' => '[REDACTED]'],
        'object'      => ['private_key' => '[REDACTED]'],
    ]);
});

test('redacts only valid non-empty change branches', function (): void {
    expect(RedactSensitiveData::redactChanges([
        'attributes' => ['password' => 'secret'],
        'old'        => 'malformed',
        'unexpected' => ['password' => 'secret'],
    ]))->toBe([
        'attributes' => ['password' => '[REDACTED]'],
    ])
        ->and(RedactSensitiveData::redactChanges([
            'old' => ['status' => 'pending'],
        ]))->toBe([
            'old' => ['status' => 'pending'],
        ])
        ->and(RedactSensitiveData::redactChanges([
            'attributes' => [],
            'old'        => [],
        ]))->toBeEmpty();
});
