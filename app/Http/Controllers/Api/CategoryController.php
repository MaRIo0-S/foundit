<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ItemResource;
use App\Models\Category;
use App\Services\CategoriesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(private CategoriesService $categoriesService) {}

    public function index(): JsonResponse
    {
        $categories = $this->categoriesService->categories();

        return response()->json([
            'message' => 'Catégories récupérées avec succès.',
            'data' => CategoryResource::collection($categories),
        ]);
    }

    public function show(Request $request, Category $category): JsonResponse
    {
        $category = $this->categoriesService->category($category->id);
        $items = $category->getRelation('items');

        return response()->json([
            'message' => 'Catégorie récupérée avec succès.',
            'data' => [
                'category' => new CategoryResource($category),
                'items' => ItemResource::collection($items)->response()->getData(true),
            ],
        ]);
    }
}
