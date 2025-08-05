<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CustomerRegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|min:6'
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        // Langsung login setelah register
        Auth::guard('customer')->login($customer);

        return redirect()->back()->with('success', 'Registrasi berhasil, Anda sudah login.');
    }
}

