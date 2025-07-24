<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function index()
    {
        return view('dashboard.profil');
    }

    public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:6|confirmed',
    ]);

    $user = Auth::user();

    // Cek apakah password lama benar
    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors([
            'current_password' => 'Password lama salah.'
        ])->with('error', true);
    }

    // Cek apakah password baru sama dengan yang lama
    if (Hash::check($request->new_password, $user->password)) {
        return back()->withErrors([
            'new_password' => 'Password baru tidak boleh sama dengan password lama.'
        ])->with('error', true);
    }

    // Simpan password baru
    $user->password = Hash::make($request->new_password);
    $user->save();

    return back()->with('success', 'Password berhasil diperbarui.');
}

}
