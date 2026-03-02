<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('pages.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'password.required' => 'Password is required',
        ]);

        if (! User::where('email', $request->email)->exists()) {
            return back()->withErrors(['status' => 'Account not found']);
        }

        if (! auth()->attempt($request->only('email', 'password'))) {
            return back()->withErrors(['status' => 'Invalid credentials']);
        }

        return redirect()->route('template');

    }

    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate the session to prevent reuse
        $request->session()->invalidate();

        // Regenerate the CSRF token for security
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
