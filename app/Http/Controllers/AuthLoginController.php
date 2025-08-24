<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AuthLoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthLoginController extends Controller
{
    /**
     * Display the login form.
     */
    public function login(): View
    {
        return view('auth.login');
    }
    
    public function signup(): View
    {
        return view('auth.signup');
    }

    public function user_dashboard(): View
    {
        return view('auth.user_dashboard');
    }

    /**
     * Handle the login request.
     */
    public function store(AuthLoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        return redirect()->intended('/');
    }
}
