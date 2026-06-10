<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class UserService
{

    public function __construct(){}

    public function reclamations()
    {
        return Auth::user()
            ->claims()
            ->with([
                'item.location',
                'item.category',
            ])
            ->get();
    }
    public function declarations()
    {
        return Auth::user()
            ->items()
            ->with([
                'location',
                'category',
            ])
            ->get();
    }
}