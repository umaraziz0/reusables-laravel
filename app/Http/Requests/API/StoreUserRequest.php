<?php

declare(strict_types=1);

namespace App\Http\Requests\API;

use App\Actions\Fields\NormalizeEmail;
use App\Actions\Fields\NormalizeName;
use App\Actions\Logging\RedactSensitiveData;
use App\Actions\Logging\WriteLog;
use App\Concerns\PasswordValidationRules;
use App\Models\User;
use App\Rules\ValidDialCode;
use App\Rules\ValidIndonesianPhoneNumber;
use App\Rules\ValidName;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class StoreUserRequest extends FormRequest
{
    use PasswordValidationRules;

    /**
     * Prepare the request data for validation.
     */
    #[Override]
    protected function prepareForValidation(): void
    {
        $normalizedInput = [];

        $name = $this->input('name');

        if (is_string($name)) {
            $normalizedInput['name'] = resolve(NormalizeName::class)->handle($name);
        }

        $email = $this->input('email');

        if (is_string($email)) {
            $normalizedInput['email'] = resolve(NormalizeEmail::class)->handle($email);
        }

        $this->merge($normalizedInput);
    }

    /**
     * Handle a failed validation attempt.
     */
    #[Override]
    protected function failedValidation(Validator $validator): void
    {
        $failedFields = array_keys($validator->errors()->messages());

        $redactedRequest = collect($this->validationData())
            ->except($failedFields)
            ->except(['password', 'password_confirmation'])
            ->map(static fn (mixed $value, string $key): mixed => match ($key) {
                'name'         => RedactSensitiveData::redactName((string) $value),
                'email'        => RedactSensitiveData::redactEmail((string) $value),
                'phone_number' => RedactSensitiveData::redactPhone((string) $value),
                default        => $value,
            })
            ->toArray();

        resolve(WriteLog::class)->handle(
            level: 'error',
            module: 'API',
            action: 'Create User',
            message: 'validation failed',
            context: [
                'errors'  => $validator->errors()->toArray(),
                'request' => $redactedRequest,
            ],
        );

        parent::failedValidation($validator);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255', new ValidName],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password'     => $this->passwordRules(),
            'country_code' => ['nullable', 'string', new ValidDialCode],
            'phone_number' => ['nullable', 'string', new ValidIndonesianPhoneNumber],
        ];
    }
}
