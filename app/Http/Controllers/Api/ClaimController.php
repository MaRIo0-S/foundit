<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClaimRequest;
use App\Http\Resources\ClaimResource;
use App\Models\Claim;
use App\Models\Item;
use App\Services\ClaimsService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    public function __construct(
        private ClaimsService $claimsService,
        private UserService $userService,
    ) {}

    public function index(): JsonResponse
    {
        $claims = $this->userService->reclamations();

        return response()->json([
            'message' => 'Réclamations récupérées avec succès.',
            'data' => ClaimResource::collection($claims),
        ]);
    }

    public function show(Request $request, Claim $claim): JsonResponse
    {
        if (!$this->canAccessClaim($request, $claim)) {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à consulter cette réclamation.',
            ], 403);
        }

        $claim->load([
            'item.category:id,name',
            'item.location:id,name',
            'item.user:id,name,email',
            'user:id,name,email',
            'reviewer:id,name,email',
        ]);

        return response()->json([
            'message' => 'Réclamation récupérée avec succès.',
            'data' => new ClaimResource($claim),
        ]);
    }

    public function store(ClaimRequest $request, Item $item): JsonResponse
    {
        if ($this->claimsService->store($request->validated(), $item->id)) {
            $claim = Claim::query()
                ->where('item_id', $item->id)
                ->where('user_id', $request->user()->id)
                ->latest()
                ->first();

            $claim?->load([
                'item.category:id,name',
                'item.location:id,name',
                'user:id,name,email',
            ]);

            return response()->json([
                'message' => 'Réclamation soumise avec succès.',
                'data' => new ClaimResource($claim),
            ], 201);
        }

        return response()->json([
            'message' => 'Vous avez déjà réclamé cet objet.',
            'errors' => [
                'item' => ['Vous avez déjà réclamé cet objet.'],
            ],
        ], 422);
    }

    public function update(ClaimRequest $request, Claim $claim): JsonResponse
    {
        $this->claimsService->update($claim, $request->validated());

        $claim->load([
            'item.category:id,name',
            'item.location:id,name',
            'user:id,name,email',
            'reviewer:id,name,email',
        ]);

        return response()->json([
            'message' => 'Réclamation modifiée avec succès.',
            'data' => new ClaimResource($claim),
        ]);
    }

    public function destroy(Request $request, Claim $claim): JsonResponse
    {
        $this->claimsService->delete($claim);

        return response()->json([
            'message' => 'Réclamation supprimée avec succès.',
        ]);
    }

    private function canAccessClaim(Request $request, Claim $claim): bool
    {
        $user = $request->user();

        return $user->isAdmin() || $user->id === $claim->user_id;
    }
}
