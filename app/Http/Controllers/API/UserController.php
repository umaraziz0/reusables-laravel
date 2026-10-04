<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Actions\Logging\LogActivity;
use App\Actions\Logging\LogException;
use App\Actions\Logging\RedactSensitiveData;
use App\Actions\Logging\WriteLog;
use App\Actions\User\CreateUser;
use App\Actions\User\GetPaginatedUsers;
use App\Actions\User\GetUserByUuid;
use App\Actions\User\GetUsers;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class UserController extends Controller
{
    use ApiResponseHelpers;

    /**
     * Get All Users
     */
    public function index(GetUsers $getUsers): JsonResponse
    {
        $users = $getUsers->handle();

        return $this->respondSuccess(
            message: 'Users retrieved successfully',
            data: UserResource::collection($users)
        );
    }

    /**
     * Get Paginated Users
     */
    public function indexPaginated(GetPaginatedUsers $getPaginatedUsers): JsonResponse
    {
        $users = $getPaginatedUsers->handle();

        return $this->respondSuccessWithPagination(
            collection: UserResource::collection($users),
            message: 'Users retrieved successfully'
        );
    }

    /**
     * Create a new user.
     */
    public function store(
        StoreUserRequest $request,
        CreateUser $createUser,
        WriteLog $writeLog,
        LogActivity $logActivity,
        LogException $logException,
    ): JsonResponse {
        try {
            $user = $createUser->handle($request->validated());
        } catch (Throwable $exception) {
            $logException->handle(
                module: 'API',
                action: 'Create User',
                message: 'failed to create user',
                exception: $exception,
                additionalContext: [
                    'email' => RedactSensitiveData::redactEmail((string) $request->validated('email')),
                ],
            );

            return $this->respondServerError(message: 'Failed to create user');
        }

        $writeLog->handle(
            level: 'info',
            module: 'API',
            action: 'Create User',
            message: 'user created',
            context: [
                'user_uuid' => $user->uuid,
                'email'     => RedactSensitiveData::redactEmail($user->email),
            ],
        );

        $logActivity->handle(
            module: 'API',
            action: 'Create User',
            message: 'user created',
            performedOn: $user,
        );

        return $this->respondCreated(
            message: 'User created successfully',
            data: UserResource::make($user)
        );
    }

    /**
     * Get User by UUID
     */
    public function show(GetUserByUuid $getUserByUuid, string $uuid): JsonResponse
    {
        $user = $getUserByUuid->handle($uuid);

        if (! $user instanceof User) {
            return $this->respondNotFound(message: 'User not found');
        }

        return $this->respondSuccess(
            message: 'User retrieved successfully',
            data: UserResource::make($user)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): void
    {
        //
    }
}
