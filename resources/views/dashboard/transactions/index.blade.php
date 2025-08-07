@extends('dashboard.layouts.main')

@section('content')
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Manajemen Keuangan</h2>
            <a href="{{ route('keuangan.create') }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                + Transaksi
            </a>
        </div>

        {{-- Notifikasi sukses --}}
        @if (session('success'))
            <div id="success-alert"
                class="mb-4 flex items-center justify-between p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg shadow-sm">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()"
                    class="text-green-600 hover:text-green-800 text-sm">&times;</button>
            </div>
        @endif

        {{-- Layout dua kolom --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            {{-- Kolom kiri: Kategori + Metode --}}
            <div class="space-y-6">

                {{-- Kategori Transaksi --}}
                <div>
                    <h3 class="text-xl font-semibold mb-3">Kategori Transaksi</h3>
                    <div class="bg-white shadow-md rounded-lg overflow-hidden p-4">
                        <form action="{{ route('keuangan.categories.store') }}" method="POST" class="flex mb-4 space-x-2">
                            @csrf
                            <input type="text" name="name" placeholder="Nama kategori"
                                class="border rounded px-3 py-2 w-full" required>
                            <button type="submit"
                                class="bg-green-600 text-white px-3 py-2 rounded flex items-center justify-center"
                                title="Tambah Kategori">
                                <i class="fas fa-plus"></i>
                            </button>
                        </form>

                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2">No</th>
                                    <th class="px-4 py-2">Nama Kategori</th>
                                    <th class="px-4 py-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categories as $i => $category)
                                    <tr class="border-b">
                                        <td class="px-4 py-2">{{ $i + 1 }}</td>
                                        <td class="px-4 py-2">{{ $category->name }}</td>
                                        <td class="px-4 py-2">
                                            <form action="{{ route('keuangan.categories.destroy', $category->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button onclick="return confirm('Hapus kategori ini?')"
                                                    class="text-red-500 hover:text-red-700">
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

            </div>

            {{-- Kolom kanan: Daftar Transaksi --}}
            <div class="lg:col-span-3">
                <h3 class="text-xl font-semibold mb-3">Daftar Transaksi</h3>
                <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-gray-100 text-gray-700 uppercase">
                            <tr>
                                <th class="px-2 py-2 w-12">No</th>
                                <th class="px-2 py-2 w-24">Tanggal</th>
                                <th class="px-2 py-2">Judul</th>
                                <th class="px-2 py-2 w-20">Jenis</th>
                                <th class="px-2 py-2 w-28">Kategori</th>
                                <th class="px-2 py-2 w-28">Nominal</th>
                                <th class="px-2 py-2 w-28">Metode</th>
                                <th class="px-2 py-2">Keterangan</th>
                                <th class="px-2 py-2 w-16 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $index => $transaction)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-2 py-2">{{ $index + $transactions->firstItem() }}</td>
                                    <td class="px-2 py-2">
                                        {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}</td>
                                    <td class="px-2 py-2">{{ $transaction->title }}</td>
                                    <td class="px-2 py-2">
                                        @if ($transaction->type === 'income')
                                            <span
                                                class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">Pemasukan</span>
                                        @else
                                            <span
                                                class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">Pengeluaran</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2">{{ $transaction->category->name ?? '-' }}</td>
                                    <td class="px-2 py-2">Rp{{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td class="px-2 py-2">{{ $transaction->payment_method ?? '-' }}</td>
                                    <td class="px-2 py-2">{{ $transaction->description ?? '-' }}</td>
                                    <td class="px-4 py-2 flex space-x-2">
                                        <!-- Tombol Detail -->
                                        <!-- Tombol Detail -->
                                        <button type="button" class="text-blue-500 hover:text-blue-700"
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


                                        <!-- Tombol Edit -->
                                        <a href="{{ route('keuangan.edit', $transaction->id) }}"
                                            class="text-yellow-500 hover:text-yellow-700" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Tombol Hapus -->
                                        <form action="{{ route('keuangan.destroy', $transaction->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button onclick="return confirm('Yakin akan menghapus transaksi ini?')"
                                                class="text-red-500 hover:text-red-700" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <!-- Modal Detail -->



                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-gray-500 py-4">Tidak ada data transaksi
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <!-- Modal Detail -->
                    <div id="transactionModal"
                        class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Detail Transaksi</h3>
                                <button onclick="closeTransactionModal()"
                                    class="text-gray-500 hover:text-gray-700">&times;</button>
                            </div>
                            <div id="transactionDetail" class="space-y-2">
                                <p><strong>Tanggal:</strong> <span id="detailDate"></span></p>
                                <p><strong>Judul:</strong> <span id="detailTitle"></span></p>
                                <p><strong>Jenis:</strong> <span id="detailType"></span></p>
                                <p><strong>Kategori:</strong> <span id="detailCategory"></span></p>
                                <p><strong>Nominal:</strong> Rp<span id="detailAmount"></span></p>
                                <p><strong>Metode:</strong> <span id="detailMethod"></span></p>
                                <p><strong>Keterangan:</strong> <span id="detailDescription"></span></p>
                            </div>
                        </div>
                    </div>

                </div>
                {{ $transactions->links() }}
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
    </script>
@endsection
