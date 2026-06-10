<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\Item;
use App\Models\User;

class HomeService
{
    public function __construct(){}
    public function users(){
        return User::count();
    }
    public function claims(){
        return Claim::count();
    }

    public function items(){
        return Item::count();
    }
}
