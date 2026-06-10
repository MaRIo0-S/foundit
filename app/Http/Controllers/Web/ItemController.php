<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemRegisterRequest;
use App\Models\Item;
use App\Services\ItemsService;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    private ItemsService $itemsService;
    public function __construct(ItemsService $itemsService)
    {
        $this->itemsService = $itemsService;
    }
    public function declaration(ItemRegisterRequest $request){
        $validated = $request->validated();
        $this->itemsService->declare($validated);
        return redirect()->route('dashboard')->with('success','the item did declared with success');
    }
    public function delete(Item $item){
        $this->itemsService->delete($item);
        return redirect()->back()->with('success', 'declaration supprimer avec success');
    }

    public function update(ItemRegisterRequest $request, Item $item){
        $validated = $request->validated();
        $this->itemsService->update($validated, $item);
        return redirect()->route('profile.declarations')->with('success', 'declaration modifier avec success');
    }
}
