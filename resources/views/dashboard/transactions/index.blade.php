@extends('dashboard.layouts.main')

@section('content')
<div class="container mx-auto p-6 space-y-6">

    {{-- Header --}}
    <div class="flex justify-between items-center border-b pb-3">
        <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-wallet text-red-600"></i> Manajemen Keuangan
        </h2>
        <a href="{{ route('keuangan.create') }}"
           class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg shadow-md flex items-center gap-2 transition">
            <i class="fas fa-plus"></i> Transaksi
        </a>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div id="success-alert"
             class="flex items-center justify-between p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg shadow-sm animate-fade-in">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800 text-sm">&times;</button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- Kolom Kiri --}}
        <div class="space-y-6">

            {{-- Kategori Transaksi --}}
            <div>
                <h3 class="text-xl font-semibold mb-3 flex items-center gap-2">
                    <i class="fas fa-tags text-gray-600"></i> Kategori Transaksi
                </h3>
                <div class="bg-white shadow-md rounded-lg overflow-hidden p-4">
                    <form action="{{ route('keuangan.categories.store') }}" method="POST" class="flex mb-4 space-x-2">
                        @csrf
                        <input type="text" name="name" placeholder="Nama kategori"
                               class="border rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-red-500" required>
                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded flex items-center justify-center transition"
                                title="Tambah Kategori">
                            <i class="fas fa-plus"></i>
                        </button>
                    </form>

                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-2">No</th>
                                <th class="px-4 py-2">Nama Kategori</th>
                                <th class="px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $i => $category)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $i + 1 }}</td>
                                    <td class="px-4 py-2">{{ $category->name }}</td>
                                    <td class="px-4 py-2 text-center">
                                        <form action="{{ route('keuangan.categories.destroy', $category->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button onclick="return confirm('Hapus kategori ini?')"
                                                    class="text-red-500 hover:text-red-700 transition">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-gray-500 py-4">Tidak ada kategori</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Metode Pembayaran --}}
            {{-- (Bisa ditambahkan di sini dengan style yang sama) --}}
        </div>

        {{-- Kolom Kanan --}}
        <div class="lg:col-span-3 space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold flex items-center gap-2">
                    <i class="fas fa-list text-gray-600"></i> Daftar Transaksi
                </h3>
                <div class="flex gap-2">
                    <a href="{{ route('keuangan.export.pdf', ['type' => 'income', 'month' => date('m'), 'year' => date('Y')]) }}"
                       class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded transition">
                       Export Pemasukan
                    </a>
                    <a href="{{ route('keuangan.export.pdf', ['type' => 'expense', 'month' => date('m'), 'year' => date('Y')]) }}"
                       class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded transition">
                       Export Pengeluaran
                    </a>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700 uppercase">
                        <tr>
                            <th class="px-2 py-2">No</th>
                            <th class="px-2 py-2">Tanggal</th>
                            <th class="px-2 py-2">Judul</th>
                            <th class="px-2 py-2">Jenis</th>
                            <th class="px-2 py-2">Kategori</th>
                            <th class="px-2 py-2">Nominal</th>
                            <th class="px-2 py-2">Metode</th>
                            <th class="px-2 py-2">Keterangan</th>
                            <th class="px-2 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $index => $transaction)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-2 py-2">{{ $index + $transactions->firstItem() }}</td>
                                <td class="px-2 py-2">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}</td>
                                <td class="px-2 py-2">{{ $transaction->title }}</td>
                                <td class="px-2 py-2">
                                    @if ($transaction->type === 'income')
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">Pemasukan</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">Pengeluaran</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2">{{ $transaction->category->name ?? '-' }}</td>
                                <td class="px-2 py-2">Rp{{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                <td class="px-2 py-2">{{ $transaction->payment_method ?? '-' }}</td>
                                <td class="px-2 py-2">{{ $transaction->description ?? '-' }}</td>
                                <td class="px-4 py-2 flex justify-center gap-2">
                                    <button type="button" class="text-blue-500 hover:text-blue-700 transition"
                                        title="Lihat Detail"
                                        onclick="openTransactionModal(
                                            '{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}',
                                            '{{ $transaction->title }}',
                                            '{{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}',
                                            '{{ $transaction->category->name ?? '-' }}',
                                            '{{ number_format($transaction->amount, 0, ',', '.') }}',
                                            '{{ $transaction->paymentMethod->name ?? '-' }}',
                                            '{{ $transaction->description ?? '-' }}'
                                        )">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <form action="{{ route('keuangan.destroy', $transaction->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
    class="fas fa-trash text-red-500 hover:text-red-700 btn-delete"
    data-id="{{ $transaction->id }}"></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-gray-500 py-4">Tidak ada data transaksi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $transactions->links() }}
        </div>
    </div>

    {{-- Modal Detail --}}
    <div id="transactionModal"
    class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300 ease-out">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 transform scale-95 transition-transform duration-300 ease-out">

        <!-- Header -->
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-xl font-bold text-gray-800">📄 Detail Transaksi</h3>
            <button onclick="closeTransactionModal()"
                class="text-gray-400 hover:text-red-500 transition-colors text-2xl leading-none">&times;</button>
        </div>

        <!-- Konten -->
        <div id="transactionDetail" class="space-y-3 text-gray-700">
            <p><span class="font-semibold">📅 Tanggal:</span> <span id="detailDate" class="ml-1 text-gray-600"></span></p>
            <p><span class="font-semibold">📝 Judul:</span> <span id="detailTitle" class="ml-1 text-gray-600"></span></p>
            <p><span class="font-semibold">📂 Jenis:</span> <span id="detailType" class="ml-1 text-gray-600"></span></p>
            <p><span class="font-semibold">🏷 Kategori:</span> <span id="detailCategory" class="ml-1 text-gray-600"></span></p>
            <p><span class="font-semibold">💰 Nominal:</span> <span class="ml-1 font-bold text-green-600">Rp<span id="detailAmount"></span></span></p>
            <p><span class="font-semibold">💳 Metode:</span> <span id="detailMethod" class="ml-1 text-gray-600"></span></p>
            <p><span class="font-semibold">🗒 Keterangan:</span> <span id="detailDescription" class="ml-1 text-gray-600"></span></p>
        </div>

        <!-- Footer -->
        <div class="mt-6 flex justify-end">
            <button onclick="closeTransactionModal()"
                class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring focus:ring-red-300 transition">
                Tutup
            </button>
        </div>
    </div>
</div>

</div>

<script>
function openTransactionModal(date, title, type, category, amount, method, description) {
    document.getElementById('detailDate').textContent = date;
    document.getElementById('detailTitle').textContent = title;
    document.getElementById('detailType').textContent = type;
    document.getElementById('detailCategory').textContent = category;
    document.getElementById('detailAmount').textContent = amount;
    document.getElementById('detailMethod').textContent = method;
    document.getElementById('detailDescription').textContent = description;
    document.getElementById('transactionModal').classList.remove('hidden');
    document.getElementById('transactionModal').classList.add('flex');
}
function closeTransactionModal() {
    document.getElementById('transactionModal').classList.add('hidden');
    document.getElementById('transactionModal').classList.remove('flex');
}
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            let transactionId = this.getAttribute('data-id');

            Swal.fire({
                title: 'Hapus Data Transaksi?',
                text: "Data yang sudah dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                background: '#1f2937', // gelap
                color: '#fff',
                confirmButtonColor: '#dc2626', // merah AWR
                cancelButtonColor: '#6b7280', // abu netral
                confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus',
                cancelButtonText: '<i class="fas fa-times"></i> Batal',
                customClass: {
                    popup: 'rounded-xl shadow-lg animate__animated animate__shakeX',
                    confirmButton: 'px-4 py-2 rounded-lg font-semibold',
                    cancelButton: 'px-4 py-2 rounded-lg font-semibold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/keuangan/${transactionId}`;

                    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    let csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;

                    let methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';

                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection
