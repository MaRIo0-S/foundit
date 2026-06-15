<?php

namespace App\Services\Admin;

use App\Models\Claim;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\DB;

class ClaimWorkflowService
{
    public function __construct(
        private AuditLogService $auditLog,
        private NotificationService $notifications,
    ) {}

    public function approve(Claim $claim, User $reviewer): Claim
    {
        if ($claim->status !== 'pending') {
            abort(422, 'Cette réclamation a déjà été traitée.');
        }

        return DB::transaction(function () use ($claim, $reviewer) {
            $claim->update([
                'status' => 'approved',
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'rejection_reason' => null,
            ]);

            $claim->item->update(['status' => 'returned']);

            Claim::query()
                ->where('item_id', $claim->item_id)
                ->where('id', '!=', $claim->id)
                ->where('status', 'pending')
                ->each(fn (Claim $other) => $this->reject(
                    $other,
                    $reviewer,
                    'Cet objet a été attribué à un autre réclamant.',
                    notifyClaimant: true,
                ));

            $itemName = $claim->item->name;
            $declarantPhone = $claim->item->contact_phone;
            $claimantPhone = $claim->contact_phone;

            $this->notifications->send(
                $claim->user,
                'claim_approved',
                'Réclamation approuvée — objet restitué',
                "Votre réclamation pour « {$itemName} » a été approuvée et l'objet est marqué comme restitué. Contact du déclarant : {$declarantPhone}.",
                'claim',
                $claim->id,
            );

            if ($claim->item->user) {
                $this->notifications->send(
                    $claim->item->user,
                    'item_returned',
                    'Objet restitué',
                    "Votre objet « {$itemName} » a été restitué au réclamant. Contact : {$claimantPhone}.",
                    'item',
                    $claim->item_id,
                );
            }

            $this->auditLog->log(
                'claim.approved',
                "Réclamation #{$claim->id} approuvée et objet « {$itemName} » restitué",
                $claim,
                ['item_id' => $claim->item_id],
            );

            return $claim->fresh(['item.category', 'item.location', 'user', 'reviewer']);
        });
    }

    public function reject(
        Claim $claim,
        User $reviewer,
        string $reason,
        bool $notifyClaimant = true,
    ): Claim {
        if ($claim->status !== 'pending') {
            abort(422, 'Cette réclamation a déjà été traitée.');
        }

        return DB::transaction(function () use ($claim, $reviewer, $reason, $notifyClaimant) {
            $claim->update([
                'status' => 'rejected',
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'rejection_reason' => $reason,
            ]);

            $hasApproved = Claim::query()
                ->where('item_id', $claim->item_id)
                ->where('status', 'approved')
                ->exists();

            if (!$hasApproved && $claim->item->status === 'available') {
                $claim->item->update(['status' => 'available']);
            }

            if ($notifyClaimant) {
                $this->notifications->send(
                    $claim->user,
                    'claim_rejected',
                    'Réclamation refusée',
                    "Votre réclamation pour « {$claim->item->name} » a été refusée : {$reason}",
                    'claim',
                    $claim->id,
                );
            }

            $this->auditLog->log(
                'claim.rejected',
                "Réclamation #{$claim->id} rejetée",
                $claim,
                ['reason' => $reason],
            );

            return $claim->fresh(['item.category', 'item.location', 'user', 'reviewer']);
        });
    }
}
