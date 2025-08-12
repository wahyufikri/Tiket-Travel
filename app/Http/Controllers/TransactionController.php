<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Order;
use App\Models\TransactionCategory;
use App\Models\PaymentMethod;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Menampilkan daftar transaksi, kategori, dan metode pembayaran
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $transactions = Transaction::with('order')
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%");
            })
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);

        $categories = TransactionCategory::all();
        $paymentMethods = PaymentMethod::all();

        return view('dashboard.transactions.index', compact(
            'transactions',
            'categories',
            'paymentMethods'
        ));
    }

    /**
     * Form tambah transaksi
     */
    public function create()
    {
        $orders = Order::all();
        $categories = TransactionCategory::all();
        $paymentMethods = PaymentMethod::all();

        return view('dashboard.transactions.create', compact(
            'orders',
            'categories',
            'paymentMethods'
        ));
    }

    /**
     * Simpan transaksi baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'             => 'required|in:income,expense',
            'order_id'         => 'nullable|exists:orders,id',
            'title'            => 'required|string|max:255',
            'amount'           => 'required|numeric|min:0',
            'category_id'         => 'required|string|max:100',
            'transaction_date' => 'required|date',
            'payment_method'   => 'required|string|max:50',
            'description'      => 'nullable|string',
        ]);

        Transaction::create($validated);

        return redirect()->route('keuangan.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }





    /**
     * Form edit transaksi
     */
    public function edit(Transaction $transaction)
    {
        $orders = Order::all();
        $categories = TransactionCategory::all();
        $paymentMethods = PaymentMethod::all();

        return view('dashboard.transactions.edit', compact(
            'transaction',
            'orders',
            'categories',
            'paymentMethods'
        ));
    }

    /**
     * Update transaksi
     */
    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'type'             => 'required|in:income,expense',
            'order_id'         => 'nullable|exists:orders,id',
            'title'            => 'required|string|max:255',
            'amount'           => 'required|numeric|min:0',
            'category'         => 'nullable|string|max:100',
            'transaction_date' => 'required|date',
            'payment_method'   => 'nullable|string|max:50',
            'description'      => 'nullable|string',
        ]);

        $transaction->update($validated);

        return redirect()->route('keuangan.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Hapus transaksi
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->route('keuangan.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Simpan kategori transaksi
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        TransactionCategory::create(['name' => $request->name]);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Hapus kategori transaksi
     */
    public function destroyCategory($id)
{
    $category = TransactionCategory::findOrFail($id);
    $category->delete();

    return back()->with('success', 'Kategori berhasil dihapus.');
}

    /**
     * Simpan metode pembayaran
     */
    public function storePaymentMethod(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        PaymentMethod::create(['name' => $request->name]);

        return back()->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    /**
     * Hapus metode pembayaran
     */
    public function destroyPaymentMethod(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return back()->with('success', 'Metode pembayaran berhasil dihapus.');
    }

    public function exportPdf($type, $month, $year)
{
    $transactions = Transaction::with('category')
        ->where('type', $type)
        ->whereMonth('transaction_date', $month)
        ->whereYear('transaction_date', $year)
        ->orderBy('transaction_date', 'asc')
        ->get();

    $title = ucfirst($type) . ' Bulan ' . Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');

    $pdf = Pdf::loadView('dashboard.transactions.export', [
        'transactions' => $transactions,
        'title' => $title,
        'type' => $type
    ])->setPaper('a4', 'portrait');

    return $pdf->download("{$type}_{$month}_{$year}.pdf");
}
}
