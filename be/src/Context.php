<?php

declare(strict_types=1);

namespace App;

use App\Entities\User;

class Context
{
    public ?string $authenticatedUserId = null;
}
