@extends('dashboard.layouts.main')

@section('content')
<div class="container mx-auto p-6">

    {{-- ====== Summary Cards ====== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Orders</p>
                <p class="text-2xl font-bold">{{ $totalOrders ?? 0 }}</p>
            </div>
            <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Customers</p>
                <p class="text-2xl font-bold">{{ $totalCustomers ?? 0 }}</p>
            </div>
            <div class="bg-green-100 text-green-600 p-3 rounded-full">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Pendapatan Bulan Ini</p>
                <p class="text-2xl font-bold">Rp {{ number_format($monthlyIncome ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="bg-yellow-100 text-yellow-600 p-3 rounded-full">
                <i class="fas fa-coins"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Pesanan Pending</p>
                <p class="text-2xl font-bold">{{ $pendingOrders ?? 0 }}</p>
            </div>
            <div class="bg-red-100 text-red-600 p-3 rounded-full">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
    </div>

    {{-- ====== Chart Section ====== --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4">Pendapatan vs Pengeluaran</h2>
        <canvas id="incomeOutcomeChart"></canvas>
    </div>

    {{-- ====== Recent Orders Table ====== --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Pesanan Terbaru</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-2 text-left">#</th>
                        <th class="px-4 py-2 text-left">Customer</th>
                        <th class="px-4 py-2 text-left">Tanggal</th>
                        <th class="px-4 py-2 text-left">Total</th>
                        <th class="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders ?? [] as $order)
                    <tr class="border-b dark:border-gray-600">
                        <td class="px-4 py-2">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2">{{ $order->customer_name }}</td>
                        <td class="px-4 py-2">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-2">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-2">
                            @if($order->status === 'Pending')
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pending</span>
                            @elseif($order->status === 'Selesai')
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Selesai</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">{{ $order->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada pesanan terbaru</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ====== Chart.js Script ====== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('incomeOutcomeChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels ?? []),
            datasets: [
                {
                    label: 'Pendapatan',
                    data: @json($income ?? []),
                    borderColor: 'rgba(34, 197, 94, 1)',
                    backgroundColor: 'rgba(34, 197, 94, 0.2)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Pengeluaran',
                    data: @json($outcome ?? []),
                    borderColor: 'rgba(239, 68, 68, 1)',
                    backgroundColor: 'rgba(239, 68, 68, 0.2)',
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { labels: { color: '#fff' } }
            },
            scales: {
                x: { ticks: { color: '#fff' } },
                y: { ticks: { color: '#fff' }, beginAtZero: true }
            }
        }
    });
});
</script>
@endsection
