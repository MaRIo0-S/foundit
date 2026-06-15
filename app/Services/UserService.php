<?php

namespace App\Services;

use App\Models\Claim;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function reclamations()
    {
        return Auth::user()
            ->claims()
            ->with([
                'item.location',
                'item.category',
            ])
            ->get()
            ->each(function (Claim $claim) {
                if ($claim->status === 'approved' && $claim->item) {
                    $claim->setAttribute(
                        'declarant_contact_phone',
                        $claim->item->contact_phone,
                    );
                    $claim->setAttribute(
                        'declarant_name',
                        $claim->item->user?->name,
                    );
                }
            });
    }

    public function declarations()
    {
        return Auth::user()
            ->items()
            ->with([
                'location',
                'category',
                'claims' => fn ($q) => $q
                    ->where('status', 'approved')
                    ->with('user:id,name'),
            ])
            ->get()
            ->each(function ($item) {
                $approvedClaim = $item->claims->first();
                if ($approvedClaim) {
                    $item->setAttribute(
                        'claimant_contact_phone',
                        $approvedClaim->contact_phone,
                    );
                    $item->setAttribute(
                        'claimant_name',
                        $approvedClaim->user?->name,
                    );
                }
            });
    }
}
