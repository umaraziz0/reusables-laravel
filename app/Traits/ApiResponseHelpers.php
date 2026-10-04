<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use JsonSerializable;

/**
 * Trait for API response helpers.
 * Provides standardized JSON response methods for consistent API responses.
 */
trait ApiResponseHelpers
{
    // --- Success --- //

    /**
     * Respond with a success message.
     * Status code: `200`.
     *
     * @param  array<int|string, mixed>|Arrayable<int|string, mixed>|JsonSerializable|null  $data
     */
    public function respondSuccess(
        string $message = 'Success',
        null|array|Arrayable|JsonSerializable $data = null,
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $this->normalizeResponseData($data),
        ], Response::HTTP_OK);
    }

    /**
     * Respond with a success message and pagination data.
     * Status code: `200`.
     */
    public function respondSuccessWithPagination(
        ResourceCollection $collection,
        string $message = 'Success'
    ): JsonResponse {
        /** @var array<string, mixed> $responseData */
        $responseData = $collection
            ->response()
            ->getData(true);

        return response()->json([
            'success' => true,
            'message' => $message,
            ...$responseData,
        ], Response::HTTP_OK);
    }

    /**
     * Respond with a created message.
     * Status code: `201`.
     *
     * @param  array<int|string, mixed>|Arrayable<int|string, mixed>|JsonSerializable|null  $data
     */
    public function respondCreated(
        string $message = 'Created',
        null|array|Arrayable|JsonSerializable $data = null
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $this->normalizeResponseData($data),
        ], Response::HTTP_CREATED);
    }

    // --- Error --- //

    /**
     * Respond with a bad request message.
     * Status code: `400`.
     *
     * @param  array<int|string, mixed>|Arrayable<int|string, mixed>|JsonSerializable|null  $data
     * @param  array<string, mixed>|null  $errors
     */
    public function respondBadRequest(
        string $message = 'Bad Request',
        null|array|Arrayable|JsonSerializable $data = null,
        ?array $errors = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => $this->normalizeResponseData($data),
            'errors'  => $errors,
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Respond with an unauthorized message.
     * Status code: `401`.
     *
     * @param  array<int|string, mixed>|Arrayable<int|string, mixed>|JsonSerializable|null  $data
     */
    public function respondUnauthorized(
        string $message = 'Unauthorized',
        null|array|Arrayable|JsonSerializable $data = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => $this->normalizeResponseData($data),
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Respond with a forbidden message.
     * Status code: `403`.
     *
     * @param  array<int|string, mixed>|Arrayable<int|string, mixed>|JsonSerializable|null  $data
     */
    public function respondForbidden(
        string $message = 'Forbidden',
        null|array|Arrayable|JsonSerializable $data = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => $this->normalizeResponseData($data),
        ], Response::HTTP_FORBIDDEN);
    }

    /**
     * Respond with a not found message.
     * Status code: `404`.
     *
     * @param  array<int|string, mixed>|Arrayable<int|string, mixed>|JsonSerializable|null  $data
     */
    public function respondNotFound(
        string $message = 'Not Found',
        null|array|Arrayable|JsonSerializable $data = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => $this->normalizeResponseData($data),
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * Respond with a unprocessable entity message.
     * Status code: `422`.
     *
     * @param  array<int|string, mixed>|Arrayable<int|string, mixed>|JsonSerializable|null  $data
     * @param  array<string, mixed>|null  $errors
     */
    public function respondUnprocessableEntity(
        string $message = 'Unprocessable Entity',
        null|array|Arrayable|JsonSerializable $data = null,
        ?array $errors = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => $this->normalizeResponseData($data),
            'errors'  => $errors,
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Respond with a too many requests message.
     * Status code: `429`.
     *
     * @param  array<int|string, mixed>|Arrayable<int|string, mixed>|JsonSerializable|null  $data
     */
    public function respondTooManyRequests(
        string $message = 'Too Many Requests',
        null|array|Arrayable|JsonSerializable $data = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => $this->normalizeResponseData($data),
        ], Response::HTTP_TOO_MANY_REQUESTS);
    }

    /**
     * Respond with a server error message.
     * Status code: `500`.
     *
     * @param  array<int|string, mixed>|Arrayable<int|string, mixed>|JsonSerializable|null  $data
     */
    public function respondServerError(
        string $message = 'Server Error',
        null|array|Arrayable|JsonSerializable $data = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => $this->normalizeResponseData($data),
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Normalize Arrayable response data before JSON serialization.
     *
     * @param  array<int|string, mixed>|Arrayable<int|string, mixed>|JsonSerializable|null  $data
     * @return array<int|string, mixed>|JsonSerializable|null
     */
    private function normalizeResponseData(
        null|array|Arrayable|JsonSerializable $data
    ): null|array|JsonSerializable {
        return $data instanceof Arrayable ? $data->toArray() : $data;
    }
}
