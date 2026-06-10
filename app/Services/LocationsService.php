<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Location;
use Illuminate\Support\Facades\Request;

class LocationsService
{
    public function __construct(){}

    public function locations(){
        $locations = Location::all();
        return $locations;
    }
    public function location($id)
    {
        $location = Location::findOrFail($id);

        $items = Item::where('location_id', $id)
            ->with(['category:id,name'])
            ->search(Request::input('search'))
            ->status(Request::input('status'))
            ->sortDate(Request::input('sort_date'))
            ->paginate(3)
            ->withQueryString();

        $location->setRelation('items', $items);

        return $location;
    }
}
