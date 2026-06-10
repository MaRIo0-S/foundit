<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\Request;

class CategoriesService
{
    public function __construct(){}

    public function categories(){
        $categories = Category::all();
        return $categories;
    }
    public function category($id)
    {
        $category = Category::findOrFail($id);

        $items = Item::where('category_id', $id)
            ->with(['location:id,name'])
            ->search(Request::input('search'))
            ->status(Request::input('status'))
            ->sortDate(Request::input('sort_date'))
            ->paginate(3)
            ->withQueryString();

        $category->setRelation('items', $items);

        return $category;
    }
}
