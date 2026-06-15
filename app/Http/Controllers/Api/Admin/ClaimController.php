<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectClaimRequest;
use App\Http\Resources\ClaimResource;
use App\Models\Claim;
use App\Services\Admin\ClaimWorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    public function __construct(private ClaimWorkflowService $workflow) {}

    public function index(Request $request): JsonResponse
    {
        $claims = Claim::query()
            ->with([
                'item:id,name,image_path,status,category_id,location_id',
                'item.category:id,name',
                'item.location:id,name',
                'user:id,name,email',
                'reviewer:id,name,email',
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
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return response()->json([
            'message' => 'Réclamations admin récupérées avec succès.',
            'data' => ClaimResource::collection($claims)->response()->getData(true),
            'meta' => [
                'pending_count' => Claim::where('status', 'pending')->count(),
            ],
        ]);
    }

    public function show(Claim $claim): JsonResponse
    {
        $claim->load([
            'item.category:id,name',
            'item.location:id,name',
            'item.user:id,name,email',
            'user:id,name,email,created_at',
            'reviewer:id,name,email',
            'item.claims' => fn ($q) => $q->with('user:id,name,email')->where('id', '!=', $claim->id),
        ]);

        return response()->json([
            'message' => 'Réclamation récupérée avec succès.',
            'data' => new ClaimResource($claim),
        ]);
    }

    public function approve(Request $request, Claim $claim): JsonResponse
    {
        $claim = $this->workflow->approve($claim, $request->user());

        return response()->json([
            'message' => 'Réclamation approuvée avec succès.',
            'data' => new ClaimResource($claim),
        ]);
    }

    public function reject(RejectClaimRequest $request, Claim $claim): JsonResponse
    {
        $claim = $this->workflow->reject(
            $claim,
            $request->user(),
            $request->validated('rejection_reason'),
        );

        return response()->json([
            'message' => 'Réclamation rejetée.',
            'data' => new ClaimResource($claim),
        ]);
    }
}
