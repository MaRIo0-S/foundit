<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClaimRequest;
use App\Models\Claim;
use App\Services\ClaimsService;

class ClaimsController extends Controller
{
    private ClaimsService $service;
    public function __construct(ClaimsService $service)
    {
        $this->service = $service;
    }
    public function store(ClaimRequest $request, $item){
        $validated = $request->validated();
        if ($this->service->store($validated, $item)) {
            return redirect()->back()->with('success', 'Réclamation soumise avec succès.');
        }

        return redirect()->back()->with('error', 'Vous avez déjà réclamé cet objet.');
    }

    public function delete(Claim $claim)
    {
        $this->service->delete($claim);

        return redirect()->back()->with('success', 'Réclamation supprimée avec succès.');
    }

    public function update(ClaimRequest $request, Claim $claim)
    {
        $validated = $request->validated();
        $this->service->update($claim, $validated);

        return redirect()->route('profile.reclamations')->with('success', 'Réclamation modifiée avec succès.');
    }
}
