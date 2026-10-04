<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    public function __construct(User $resource)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,

            'name'  => $this->name,
            'email' => $this->email,

            'country_code' => $this->country_code,
            'phone_number' => $this->phone_number,

            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
