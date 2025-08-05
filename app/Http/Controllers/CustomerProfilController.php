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

    public function edit()
    {
        $customer = Auth::guard('customer')->user();
        return view('customer.edit-profile', compact('customer'));
    }

    public function update(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $customer->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('customer.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function logout()
    {
        Auth::guard('customer')->logout();
        return redirect('/')->with('success', 'Anda telah logout.');
    }
}
