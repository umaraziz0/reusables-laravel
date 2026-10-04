<?php

declare(strict_types=1);

namespace App\Database\Expressions;

final class UserSearchVector
{
    /**
     * PostgreSQL expression for unified user search across name, email, and phone.
     */
    public const string TRGM_INDEX = "(name::text || ' ' || email::text || ' ' || coalesce(country_code, '') || coalesce(phone_number, '')) gin_trgm_ops";

    public const string ILIKE_COMPARISON = "(name::text || ' ' || email::text || ' ' || coalesce(country_code, '') || coalesce(phone_number, '')) ILIKE ?";
}
