<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class ItemsService{
    public function __construct(){}
    public function Items()
    {
        return Item::with(['category:id,name', 'location:id,name'])
            ->search(Request::input('search'))
            ->status(Request::input('status'))
            ->sortDate(Request::input('sort_date'))
            ->paginate(3)
            ->withQueryString();
    }
    public function declare(array $data){
        $data['image_path']->store('items', 'public');
        $fileName = $data['image_path']->hashName();
        Item::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'image_path' => $fileName,
            'category_id' => $data['category_id'],
            'location_id' => $data['location_id'],
            'status' => 'available',
            'found_date' => $data['found_date'],
            'user_id' => Auth::id()
        ]);
    }

    public function delete(Item $item){
        if(Auth::id() != $item->user_id){
            abort(403, 'Vous n\'êtes pas autorisé à supprimer cette declaration.');
        }
        $item->delete();
    }
    public function update(array $data,Item $item)
    {
        $updateData = [
            'name' => $data['name'],
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'location_id' => $data['location_id'],
            'found_date' => $data['found_date'],
        ];

        if (isset($data['image_path']) && $data['image_path'] instanceof \Illuminate\Http\UploadedFile) {
            
            if ($item->image_path) {
                Storage::disk('public')->delete('items/' . $item->image_path);
            }

            $data['image_path']->store('items', 'public');
            $updateData['image_path'] = $data['image_path']->hashName();
        }

        $item->update($updateData);
    }
}
