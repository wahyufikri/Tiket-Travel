<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardAdminController extends Controller
{
    public function Dashboard()
    {
        $monthlyIncome = Transaction::where('type', 'income')
        ->whereMonth('created_at', now()->month)
        ->sum('amount');

    $monthlyExpense = Transaction::where('type', 'expense')
        ->whereMonth('created_at', now()->month)
        ->sum('amount');

    // Data untuk grafik 6 bulan terakhir
    $labels = collect(range(5, 0))->map(function ($i) {
        return now()->subMonths($i)->format('F');
    });

    $incomeData = collect(range(5, 0))->map(function ($i) {
        return Transaction::where('type', 'income')
            ->whereMonth('created_at', now()->subMonths($i)->month)
            ->sum('amount');
    });

    $expenseData = collect(range(5, 0))->map(function ($i) {
        return Transaction::where('type', 'expense')
            ->whereMonth('created_at', now()->subMonths($i)->month)
            ->sum('amount');
    });

       return view('dashboard.welcome', [
    'totalOrders' => Order::count(),
    'totalCustomers' => Customer::count(),
    'monthlyIncome' => $monthlyIncome,
    'monthlyExpense' => $monthlyExpense,
    'pendingOrders' => Order::where('order_status', 'menunggu')->count(),
    'recentOrders' => Order::latest()->take(5)->get(),
    'labels' => $labels,
    'income' => $incomeData,
    'expense' => $expenseData,
]);

    }
}
