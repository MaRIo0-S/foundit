<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\Request;

class CategoriesService
{
    public function categories()
    {
        return Category::all();
    }

    public function category($id)
    {
        $category = Category::findOrFail($id);

        $items = Item::where('category_id', $id)
            ->with(['location:id,name'])
            ->search(Request::input('search'))
            ->when(
                Request::input('status'),
                fn ($q, $status) => $q->where('status', $status),
                fn ($q) => $q->where('status', 'available'),
            )
            ->sortDate(Request::input('sort_date'))
            ->paginate(3)
            ->withQueryString();

        $category->setRelation('items', $items);

        return $category;
    }
}
