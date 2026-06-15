<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\ClaimResource;
use App\Http\Resources\ItemResource;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function __construct(
        private UserService $userService,
        private AuthService $authService,
    ) {}

    public function declarations(): JsonResponse
    {
        $items = $this->userService->declarations();

        return response()->json([
            'message' => 'Déclarations récupérées avec succès.',
            'data' => ItemResource::collection($items),
        ]);
    }

    public function reclamations(): JsonResponse
    {
        $claims = $this->userService->reclamations();

        return response()->json([
            'message' => 'Réclamations récupérées avec succès.',
            'data' => ClaimResource::collection($claims),
        ]);
    }

    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = $this->authService->update($request->validated());

        return response()->json([
            'message' => 'Profil modifié avec succès.',
            'data' => new UserResource($user),
        ]);
    }
}
