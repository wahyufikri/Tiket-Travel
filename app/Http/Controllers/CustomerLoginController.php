<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerLoginController extends Controller
{
    public function login(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required'
    ]);

    if (Auth::guard('customer')->attempt([
        'email' => $request->email,
        'password' => $request->password
    ])) {
        $request->session()->regenerate();
        return redirect()->intended(url()->previous())->with('success', 'Login berhasil.');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->onlyInput('email');
}


    public function profile()
    {

    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Berhasil logout.');
    }
}
