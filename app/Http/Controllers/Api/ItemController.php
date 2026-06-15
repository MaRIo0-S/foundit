<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemRegisterRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Services\ItemsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct(private ItemsService $itemsService) {}

    public function index(Request $request): JsonResponse
    {
        $items = $this->itemsService->Items();
        $items->load(['category:id,name', 'location:id,name', 'user:id,name,email']);

        return response()->json([
            'message' => 'Objets récupérés avec succès.',
            'data' => ItemResource::collection($items)->response()->getData(true),
        ]);
    }

    public function show(Item $item): JsonResponse
    {
        $item->load([
            'category:id,name',
            'location:id,name',
            'user:id,name,email',
        ]);

        return response()->json([
            'message' => 'Objet récupéré avec succès.',
            'data' => new ItemResource($item),
        ]);
    }

    public function store(ItemRegisterRequest $request): JsonResponse
    {
        $item = $this->itemsService->declare($request->validated());
        $item->load(['category:id,name', 'location:id,name', 'user:id,name,email']);

        return response()->json([
            'message' => 'Objet déclaré avec succès.',
            'data' => new ItemResource($item),
        ], 201);
    }

    public function update(ItemRegisterRequest $request, Item $item): JsonResponse
    {
        if ($request->user()->id !== $item->user_id && !$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à modifier cette déclaration.',
            ], 403);
        }

        $item = $this->itemsService->update($request->validated(), $item);

        return response()->json([
            'message' => 'Déclaration modifiée avec succès.',
            'data' => new ItemResource($item),
        ]);
    }

    public function destroy(Request $request, Item $item): JsonResponse
    {
        $this->itemsService->delete($item);

        return response()->json([
            'message' => 'Déclaration supprimée avec succès.',
        ]);
    }
}
