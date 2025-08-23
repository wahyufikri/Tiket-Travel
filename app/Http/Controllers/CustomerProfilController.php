<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class CustomerProfilController extends Controller
{
    public function index()
    {
        // Ambil data profil customer yang sedang login
        $customer = Auth::guard('customer')->user();

        // Ambil riwayat transaksi berdasarkan customer_id
        $orders = Order::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('homepage.public.profil', compact('customer', 'orders'));
    }

    public function editProfile()
{
    $customer = Auth::guard('customer')->user();
    return view('homepage.profile.edit', compact('customer'));
}

public function updateProfile(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'phone'   => 'required|string|max:20',
        ]);

        $customer = Auth::guard('customer')->user();
        $customer->update([
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
        ]);

        return redirect()->route('customer.profile')->with('success', 'Profil berhasil diperbarui!');
    }


    public function logout()
    {
        Auth::guard('customer')->logout();
        return redirect('/')->with('success', 'Anda telah logout.');
    }
}
