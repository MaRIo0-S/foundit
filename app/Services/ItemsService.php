<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class ItemsService
{
    public function Items()
    {
        $query = Item::with(['category:id,name', 'location:id,name'])
            ->search(Request::input('search'))
            ->sortDate(Request::input('sort_date'));

        if (Request::input('status')) {
            $query->status(Request::input('status'));
        } else {
            $query->where('status', 'available');
        }

        return $query
            ->paginate(Request::integer('per_page', 3))
            ->withQueryString();
    }
    public function declare(array $data): Item
    {
        $fileName = null;

        if (isset($data['image_path']) && $data['image_path'] instanceof \Illuminate\Http\UploadedFile) {
            $data['image_path']->store('items', 'public');
            $fileName = $data['image_path']->hashName();
        }

        return Item::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'admin_details' => $data['admin_details'],
            'image_path' => $fileName,
            'category_id' => $data['category_id'],
            'location_id' => $data['location_id'],
            'status' => 'available',
            'found_date' => $data['found_date'],
            'user_id' => Auth::id(),
            'contact_phone' => $data['contact_phone'],
        ]);
    }

    public function delete(Item $item){
        if(Auth::id() != $item->user_id){
            abort(403, 'Vous n\'êtes pas autorisé à supprimer cette déclaration.');
        }

        $this->ensureItemEditable($item);

        $item->delete();
    }
    public function update(array $data, Item $item): Item
    {
        if(Auth::id() != $item->user_id){
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette déclaration.');
        }

        $this->ensureItemEditable($item);

        $updateData = [
            'name' => $data['name'],
            'description' => $data['description'],
            'admin_details' => $data['admin_details'],
            'category_id' => $data['category_id'],
            'location_id' => $data['location_id'],
            'found_date' => $data['found_date'],
            'contact_phone' => $data['contact_phone'],
        ];

        if (isset($data['image_path']) && $data['image_path'] instanceof \Illuminate\Http\UploadedFile) {
            
            if ($item->image_path) {
                Storage::disk('public')->delete('items/' . $item->image_path);
            }

            $data['image_path']->store('items', 'public');
            $updateData['image_path'] = $data['image_path']->hashName();
        }

        $item->update($updateData);

        return $item->fresh(['category:id,name', 'location:id,name', 'user:id,name,email']);
    }

    private function ensureItemEditable(Item $item): void
    {
        if (in_array($item->status, ['claimed', 'returned'], true)) {
            abort(422, 'Cette déclaration ne peut plus être modifiée : le processus de restitution est terminé ou en cours.');
        }

        if ($item->claims()->where('status', 'approved')->exists()) {
            abort(422, 'Cette déclaration ne peut plus être modifiée : une réclamation a été approuvée.');
        }
    }
}
