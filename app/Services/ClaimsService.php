<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\Item;
use App\Services\Admin\NotificationService;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;

class ClaimsService
{
    public function __construct(
        private NotificationService $notifications,
        private AuditLogService $auditLog,
    ) {}

    public function store(array $data, int $item): bool
    {
        $userId = Auth::id();
        $itemModel = Item::findOrFail($item);

        if ($itemModel->user_id === $userId) {
            abort(422, 'Vous ne pouvez pas réclamer un objet que vous avez vous-même déclaré.');
        }

        if ($itemModel->status !== 'available') {
            abort(422, 'Cet objet n\'est plus disponible pour une réclamation.');
        }

        $activeClaimExists = Claim::where('item_id', $item)
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($activeClaimExists) {
            return false;
        }

        $claim = Claim::create([
            'claim_notes' => $data['claim_notes'],
            'contact_phone' => $data['contact_phone'],
            'item_id' => $item,
            'user_id' => $userId,
            'status' => 'pending',
        ]);

        $this->notifications->notifyStaff(
            'claim_submitted',
            'Nouvelle réclamation',
            "Nouvelle réclamation sur « {$itemModel->name} ».",
            'claim',
            $claim->id,
        );

        $this->auditLog->log(
            'claim.submitted',
            "Réclamation soumise pour « {$itemModel->name} »",
            $claim,
        );

        return true;
    }

    public function delete(Claim $claim): void
    {
        if (Auth::id() != $claim->user_id) {
            abort(403, 'Vous n\'êtes pas autorisé à supprimer cette réclamation.');
        }

        if ($claim->status !== 'pending') {
            abort(422, 'Seules les réclamations en attente peuvent être supprimées.');
        }

        $claim->delete();
    }

    public function update(Claim $claim, array $data): void
    {
        if (Auth::id() != $claim->user_id) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette réclamation.');
        }

        if ($claim->status !== 'pending') {
            abort(422, 'Seules les réclamations en attente peuvent être modifiées.');
        }

        $claim->update($data);
    }
}
