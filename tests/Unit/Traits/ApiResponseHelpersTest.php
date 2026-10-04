<?php

declare(strict_types=1);

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponseHelpers;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

pest()->use(RefreshDatabase::class);

$helper = new class
{
    use ApiResponseHelpers;
};

test('respondSuccess returns 200 with default message and null data', function () use ($helper): void {
    $response = $helper->respondSuccess();
    $data     = $response->getData(true);

    expect($response->getStatusCode())->toBe(Response::HTTP_OK)
        ->and($data)->toBe([
            'success' => true,
            'message' => 'Success',
            'data'    => null,
        ]);
});

test('respondSuccess returns custom message', function () use ($helper): void {
    $response = $helper->respondSuccess(message: 'Operation completed');
    $data     = $response->getData(true);

    expect($data['success'])->toBeTrue()
        ->and($data['message'])->toBe('Operation completed');
});

test('respondSuccess accepts array data', function () use ($helper): void {
    $payload  = ['id' => 1, 'name' => 'Test'];
    $response = $helper->respondSuccess(data: $payload);
    $data     = $response->getData(true);

    expect($data['success'])->toBeTrue()
        ->and($data['data'])->toBe($payload);
});

test('respondSuccess accepts Arrayable data', function () use ($helper): void {
    $payload = new class implements Arrayable
    {
        public function toArray(): array
        {
            return ['key' => 'value'];
        }
    };

    $response = $helper->respondSuccess(data: $payload);
    $data     = $response->getData(true);

    expect($data['success'])->toBeTrue()
        ->and($data['data'])->toBe(['key' => 'value']);
});

test('respondSuccess accepts JsonSerializable data', function () use ($helper): void {
    $payload = new class implements JsonSerializable
    {
        /**
         * @return array<string, bool>
         */
        public function jsonSerialize(): array
        {
            return ['serialized' => true];
        }
    };

    $response = $helper->respondSuccess(data: $payload);
    $data     = $response->getData(true);

    expect($data['success'])->toBeTrue()
        ->and($data['data'])->toBe(['serialized' => true]);
});

test('respondSuccessWithPagination returns 200 with paginated structure', function () use ($helper): void {
    $paginator  = new LengthAwarePaginator([], 0, 15);
    $collection = UserResource::collection($paginator);

    $response = $helper->respondSuccessWithPagination($collection);
    $data     = $response->getData(true);

    expect($response->getStatusCode())->toBe(Response::HTTP_OK)
        ->and($data['success'])->toBeTrue()
        ->and($data['message'])->toBe('Success')
        ->and($data)->toHaveKeys(['data', 'meta', 'links']);
});

test('respondSuccessWithPagination returns custom message', function () use ($helper): void {
    $paginator  = new LengthAwarePaginator([], 0, 15);
    $collection = UserResource::collection($paginator);

    $response = $helper->respondSuccessWithPagination($collection, message: 'Users retrieved');
    $data     = $response->getData(true);

    expect($data['success'])->toBeTrue()
        ->and($data['message'])->toBe('Users retrieved');
});

test('respondSuccessWithPagination merges collection response data', function () use ($helper): void {
    $user       = User::factory()->create();
    $paginator  = new LengthAwarePaginator([$user], 1, 15);
    $collection = UserResource::collection($paginator);

    $response = $helper->respondSuccessWithPagination($collection);
    $data     = $response->getData(true);

    expect($response->getStatusCode())->toBe(Response::HTTP_OK)
        ->and($data['data'][0]['uuid'] ?? null)->toBe($user->uuid)
        ->and($data['meta']['total'] ?? null)->toBe(1);
});

test('respondCreated returns 201 with default message', function () use ($helper): void {
    $response = $helper->respondCreated();
    $data     = $response->getData(true);

    expect($response->getStatusCode())->toBe(Response::HTTP_CREATED)
        ->and($data)->toBe([
            'success' => true,
            'message' => 'Created',
            'data'    => null,
        ]);
});

test('respondCreated accepts data', function () use ($helper): void {
    $payload  = ['id' => 42];
    $response = $helper->respondCreated(message: 'Resource created', data: $payload);
    $data     = $response->getData(true);

    expect($response->getStatusCode())->toBe(Response::HTTP_CREATED)
        ->and($data['success'])->toBeTrue()
        ->and($data['message'])->toBe('Resource created')
        ->and($data['data'])->toBe($payload);
});

test('respondBadRequest returns 400', function () use ($helper): void {
    $response = $helper->respondBadRequest();
    $data     = $response->getData(true);

    expect($response->getStatusCode())->toBe(Response::HTTP_BAD_REQUEST)
        ->and($data)->toBe([
            'success' => false,
            'message' => 'Bad Request',
            'data'    => null,
            'errors'  => null,
        ]);
});

test('respondBadRequest accepts errors array', function () use ($helper): void {
    $errors   = ['email' => ['The email field is required.']];
    $response = $helper->respondBadRequest(
        message: 'Validation failed',
        errors: $errors,
    );
    $data = $response->getData(true);

    expect($data['success'])->toBeFalse()
        ->and($data['message'])->toBe('Validation failed')
        ->and($data['errors'])->toBe($errors);
});

test('respondUnauthorized returns 401', function () use ($helper): void {
    $response = $helper->respondUnauthorized();
    $data     = $response->getData(true);

    expect($response->getStatusCode())->toBe(Response::HTTP_UNAUTHORIZED)
        ->and($data)->toBe([
            'success' => false,
            'message' => 'Unauthorized',
            'data'    => null,
        ]);
});

test('respondForbidden returns 403', function () use ($helper): void {
    $response = $helper->respondForbidden();
    $data     = $response->getData(true);

    expect($response->getStatusCode())->toBe(Response::HTTP_FORBIDDEN)
        ->and($data)->toBe([
            'success' => false,
            'message' => 'Forbidden',
            'data'    => null,
        ]);
});

test('respondNotFound returns 404', function () use ($helper): void {
    $response = $helper->respondNotFound();
    $data     = $response->getData(true);

    expect($response->getStatusCode())->toBe(Response::HTTP_NOT_FOUND)
        ->and($data)->toBe([
            'success' => false,
            'message' => 'Not Found',
            'data'    => null,
        ]);
});

test('respondUnprocessableEntity returns 422', function () use ($helper): void {
    $response = $helper->respondUnprocessableEntity();
    $data     = $response->getData(true);

    expect($response->getStatusCode())->toBe(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->and($data)->toBe([
            'success' => false,
            'message' => 'Unprocessable Entity',
            'data'    => null,
            'errors'  => null,
        ]);
});

test('respondUnprocessableEntity accepts errors array', function () use ($helper): void {
    $errors   = ['field' => ['Invalid value.']];
    $response = $helper->respondUnprocessableEntity(
        message: 'Validation error',
        errors: $errors,
    );
    $data = $response->getData(true);

    expect($data['success'])->toBeFalse()
        ->and($data['errors'])->toBe($errors);
});

test('respondTooManyRequests returns 429', function () use ($helper): void {
    $response = $helper->respondTooManyRequests();
    $data     = $response->getData(true);

    expect($response->getStatusCode())->toBe(Response::HTTP_TOO_MANY_REQUESTS)
        ->and($data)->toBe([
            'success' => false,
            'message' => 'Too Many Requests',
            'data'    => null,
        ]);
});

test('respondServerError returns 500', function () use ($helper): void {
    $response = $helper->respondServerError();
    $data     = $response->getData(true);

    expect($response->getStatusCode())->toBe(Response::HTTP_INTERNAL_SERVER_ERROR)
        ->and($data)->toBe([
            'success' => false,
            'message' => 'Server Error',
            'data'    => null,
        ]);
});

test('respondServerError accepts data', function () use ($helper): void {
    $payload  = ['trace' => 'error details'];
    $response = $helper->respondServerError(message: 'Internal error', data: $payload);
    $data     = $response->getData(true);

    expect($data['success'])->toBeFalse()
        ->and($data['message'])->toBe('Internal error')
        ->and($data['data'])->toBe($payload);
});
