<?php

declare(strict_types=1);

namespace App\Enums\Ability;

use App\Traits\BaseEnum;

enum UserAbility: string
{
    use BaseEnum;

    case View   = 'users.view';
    case Create = 'users.create';
}
