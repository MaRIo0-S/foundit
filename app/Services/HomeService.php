<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\Item;
use App\Models\User;

class HomeService
{
    public function users(): int
    {
        return User::count();
    }

    public function claims(): int
    {
        return Claim::count();
    }

    public function items(): int
    {
        return Item::count();
    }
}
