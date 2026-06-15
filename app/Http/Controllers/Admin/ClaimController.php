<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectClaimRequest;
use App\Models\Claim;
use App\Services\Admin\ClaimWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClaimController extends Controller
{
    public function __construct(private ClaimWorkflowService $workflow) {}

    public function index(Request $request): Response
    {
        $claims = Claim::query()
            ->with([
                'item:id,name,image_path,status,category_id,location_id',
                'item.category:id,name',
                'item.location:id,name',
                'user:id,name,email',
                'reviewer:id,name',
            ])
            ->when($request->input('search'), function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('claim_notes', 'ilike', "%{$search}%")
                        ->orWhereHas('item', fn ($iq) => $iq->where('name', 'ilike', "%{$search}%"))
                        ->orWhereHas('user', fn ($uq) => $uq
                            ->where('name', 'ilike', "%{$search}%")
                            ->orWhere('email', 'ilike', "%{$search}%"));
                });
            })
            ->when($request->input('status'), fn ($q, $status) => $q->where('status', $status))
            ->when(
                $request->input('sort') === 'oldest',
                fn ($q) => $q->oldest(),
                fn ($q) => $q->latest(),
            )
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Claims/Index', [
            'claims' => $claims,
            'filters' => $request->only(['search', 'status', 'sort']),
            'pendingCount' => Claim::where('status', 'pending')->count(),
        ]);
    }

    public function review(Claim $claim): Response
    {
        $claim->load([
            'item.category',
            'item.location',
            'item.user:id,name,email',
            'user:id,name,email,created_at',
            'reviewer:id,name',
            'item.claims' => fn ($q) => $q
                ->with('user:id,name')
                ->where('id', '!=', $claim->id),
        ]);

        $claim->item?->makeVisible(['contact_phone', 'admin_details']);
        $claim->makeVisible(['contact_phone']);

        return Inertia::render('Admin/Claims/Review', [
            'claim' => $claim,
        ]);
    }

    public function approve(Claim $claim): RedirectResponse
    {
        $this->workflow->approve($claim, request()->user());

        return redirect()
            ->route('admin.claims.index', ['status' => 'pending'])
            ->with('success', 'Réclamation approuvée et objet restitué.');
    }

    public function reject(RejectClaimRequest $request, Claim $claim): RedirectResponse
    {
        $this->workflow->reject($claim, $request->user(), $request->validated('rejection_reason'));

        return redirect()
            ->route('admin.claims.index', ['status' => 'pending'])
            ->with('success', 'Réclamation rejetée.');
    }
}
