<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller{
    
    private AuthService $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function Register(RegisterRequest $request)
    {
        $validated = $request->validated();
        $this->authService->register($validated);
        
        return redirect()->route('auth.login')->with('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
    }

    
    public function Login(LoginRequest $request){
        $validated = $request->validated();

        if ($this->authService->login($validated)) {
            return redirect()->route('home')->with('success', 'Connexion réussie !');
        }
        return back()->withErrors(['email' => 'Identifiants invalides.']);
    }

    public function Logout(Request $request){
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::logout();
        
        return redirect()->route('auth.login')->with('success', 'Déconnexion réussie !');
    }


    public function modify(UpdateUserRequest $request)
    {
        $this->authService->update($request->validated());
        return redirect()->route('dashboard')->with('success', 'profile a ete modifier avec success');
    }
}
