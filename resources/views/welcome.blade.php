@extends('dashboard.layouts.main')

@section('content')
<div class="container mt-5">

    {{-- Ringkasan Statistik --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Income Bulan Ini</h5>
                    <h3>Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Expense Bulan Ini</h5>
                    <h3>Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Income vs Expense --}}
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Income vs Expense (6 Bulan Terakhir)</h5>
            <canvas id="incomeExpenseChart"></canvas>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('incomeExpenseChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [
                    {
                        label: 'Income',
                        data: @json($income),
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true
                    },
                    {
                        label: 'Expense',
                        data: @json($expense),
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
@endsection
