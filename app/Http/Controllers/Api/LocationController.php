<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use App\Services\LocationsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct(private LocationsService $locationsService) {}

    public function index(): JsonResponse
    {
        $locations = $this->locationsService->locations();

        return response()->json([
            'message' => 'Lieux récupérés avec succès.',
            'data' => LocationResource::collection($locations),
        ]);
    }

    public function show(Request $request, Location $location): JsonResponse
    {
        $location = $this->locationsService->location($location->id);
        $items = $location->getRelation('items');

        return response()->json([
            'message' => 'Lieu récupéré avec succès.',
            'data' => [
                'location' => new LocationResource($location),
                'items' => ItemResource::collection($items)->response()->getData(true),
            ],
        ]);
    }
}
