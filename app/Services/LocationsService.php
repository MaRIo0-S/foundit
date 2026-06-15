<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Location;
use Illuminate\Support\Facades\Request;

class LocationsService
{
    public function locations()
    {
        return Location::all();
    }

    public function location($id)
    {
        $location = Location::findOrFail($id);

        $items = Item::where('location_id', $id)
            ->with(['category:id,name'])
            ->search(Request::input('search'))
            ->when(
                Request::input('status'),
                fn ($q, $status) => $q->where('status', $status),
                fn ($q) => $q->where('status', 'available'),
            )
            ->sortDate(Request::input('sort_date'))
            ->paginate(3)
            ->withQueryString();

        $location->setRelation('items', $items);

        return $location;
    }
}
