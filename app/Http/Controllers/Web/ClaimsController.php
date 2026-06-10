<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClaimRequest;
use App\Models\Claim;
use App\Models\Item;
use App\Services\ClaimsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ClaimsController extends Controller
{
    private ClaimsService $service;
    public function __construct(ClaimsService $service)
    {
        $this->service = $service;
    }
    public function store(ClaimRequest $request, $item){
        $validated = $request->validated();
        if($this->service->store($validated, $item)){
            return redirect()->back()->with('success', 'Claim submitted');      
        }
        return redirect()->back()->with('error', 'You did already claim this item'); 
    }
    public function delete(Claim $claim){
        $this->service->delete($claim);
        return redirect()->back()->with('success', 'reclamation supprimer avec success');
    }
    public function update(ClaimRequest $request, Claim $claim){
        $validated = $request->validated();
        $this->service->update($claim, $validated);
        return redirect()->route('profile.reclamations')->with('success', 'reclamation modifier avec success');
    }
}
