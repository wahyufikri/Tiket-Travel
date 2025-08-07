@extends('dashboard.layouts.main')

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Tambah Transaksi Baru</h2>

        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('keuangan.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4">
            @csrf

            <!-- Jenis Transaksi -->
            <div>
                <label for="type" class="block font-semibold">Jenis Transaksi <span class="text-red-500">*</span></label>
                <select name="type" id="type" required
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">-- Pilih Jenis --</option>
                    <option value="income">Pemasukan</option>
                    <option value="expense">Pengeluaran</option>
                </select>
            </div>

            <!-- Order (Opsional) -->


            <!-- Judul -->
            <div>
                <label for="title" class="block font-semibold">Judul <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    placeholder="Masukkan judul transaksi" required>
            </div>

            <!-- Nominal -->
            <div>
                <label for="amount" class="block font-semibold">Nominal (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="amount" id="amount" value="{{ old('amount') }}" min="0"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    placeholder="Masukkan nominal" required>
            </div>

            <!-- Kategori -->
            <div>
                <label for="category_id" class="block font-semibold">Kategori <span class="text-red-500">*</span></label>
                <select name="category_id" id="category_id" required
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal Transaksi -->
            <div>
                <label for="transaction_date" class="block font-semibold">Tanggal Transaksi <span
                        class="text-red-500">*</span></label>
                <input type="date" name="transaction_date" id="transaction_date"
                    value="{{ old('transaction_date', date('Y-m-d')) }}"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    required>
            </div>


            <!-- Metode Pembayaran -->
            <div>
                <label for="payment_method_id" class="block font-semibold">Metode Pembayaran <span
                        class="text-red-500">*</span></label>
                <select name="payment_method" id="payment_method" required
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">-- Pilih Metode --</option>
                    <option value="Cash">Cash</option>
                    <option value="Transfer">Transfer</option>

                </select>
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="block font-semibold">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    placeholder="Masukkan deskripsi (opsional)">{{ old('description') }}</textarea>
            </div>

            <!-- Tombol -->
            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('keuangan.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</a>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Simpan</button>
            </div>
        </form>
    </div>
@endsection
