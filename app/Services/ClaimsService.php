<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ClaimsService
{
    public function store(array $data, int $item){
        $id = Auth::id();
        $claimExists = Claim::where('item_id', $item)->where('user_id', $id)->exists();
        if(!$claimExists){
            $claim = Claim::create([
                'claim_notes' => $data['claim_notes'],
                'item_id' => $item,
                'user_id' => $id
            ]);
            $claim->item()->update(['status'=>'claimed']);
            return true;
        }
        return false;   
    }
    public function delete(Claim $claim){
        if(Auth::id() != $claim->user_id){
            abort(403, 'Vous n\'êtes pas autorisé à supprimer cette réclamation.');
        }
        $claim->delete();
    }
    public function update(Claim $claim, array $data){
        if(Auth::id() != $claim->user_id){
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette réclamation.');
        }
        $claim->update($data);
    }
}
