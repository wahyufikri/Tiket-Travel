<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Tampilkan daftar customer.
     */
    public function index()
    {
        $customers = Customer::latest()->paginate(10);
        return view('dashboard.manajemen.customer.index', compact('customers'));
    }

    /**
     * Tampilkan form tambah customer.
     */
    public function create()
    {
        return view('dashboard.manajemen.customer.create');
    }

    /**
     * Simpan customer baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:60',
            'email'    => 'required|email|unique:customers,email',
            'phone'    => 'nullable|string|max:20',

        ]);

        Customer::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('pelanggan.index')->with('success', 'Customer berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail customer (opsional).
     */
    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    /**
     * Tampilkan form edit customer.
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('dashboard.manajemen.customer.edit', compact('customer'));
    }

    /**
     * Update data customer.
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $request->validate([
            'name'  => 'required|string|max:60',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $customer->update($data);

        return redirect()->route('pelanggan.index')->with('success', 'Customer berhasil diperbarui.');
    }

    /**
     * Hapus customer.
     */
    public function destroy($id)
{
    $customer = Customer::findOrFail($id);
    $customer->delete();
    return redirect()->route('pelanggan.index')->with('success', 'Customer berhasil dihapus.');
}

}
