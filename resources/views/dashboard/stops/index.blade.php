@extends('dashboard.layouts.main')

@section('content')
<div class="container mx-auto p-6 space-y-8">

    {{-- Judul Halaman --}}
    <div class="flex justify-between items-center">
        <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-route text-blue-600"></i>
            Manajemen Pemberhentian & Harga Rute
        </h2>
    </div>

@if ($errors->any())
            <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    <div class="grid lg:grid-cols-2 gap-6">

        {{-- Tabel Harga Antar Titik --}}
        <div class="bg-white shadow-lg rounded-lg p-4">
            <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h3 class="text-xl font-semibold text-gray-700">Daftar Harga Antar Titik</h3>
                <button onclick="openModal()" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fa-solid fa-plus"></i> Tambah Harga
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-2 border">No</th>
                            <th class="px-4 py-2 border">Rute</th>
                            <th class="px-4 py-2 border">Dari</th>
                            <th class="px-4 py-2 border">Ke</th>
                            <th class="px-4 py-2 border">Harga</th>
                            <th class="px-4 py-2 border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stopPrices as $price)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2 border">{{ $price->route->origin }} - {{ $price->route->destination }}</td>
                                <td class="px-4 py-2 border">{{ $price->fromStop->stop_name }}</td>
                                <td class="px-4 py-2 border">{{ $price->toStop->stop_name }}</td>
                                <td class="px-4 py-2 border font-semibold text-green-600">Rp {{ number_format($price->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 border space-x-2">
                                    <button type="button" onclick="openModal('modalEditPrice{{ $price->id }}')"
    class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                    <i class="fa-solid fa-pen"></i>
</button>

                                    <form action="{{ route('hargapertitik.destroy', $price->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus harga ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-2 border text-center text-gray-500">Tidak ada data harga</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tabel Pemberhentian Rute --}}
        <div class="bg-white shadow-lg rounded-lg p-4">
            <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h3 class="text-xl font-semibold text-gray-700">Daftar Pemberhentian Rute</h3>
                <a href="{{ route('stop.create') }}" class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="fa-solid fa-plus"></i> Tambah Pemberhentian
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-2 border">No</th>
                            <th class="px-4 py-2 border">Rute</th>
                            <th class="px-4 py-2 border">Urutan</th>
                            <th class="px-4 py-2 border">Nama Pemberhentian</th>
                            <th class="px-4 py-2 border">Durasi (menit)</th>
                            <th class="px-4 py-2 border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($routeStops as $stop)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2 border">{{ $stop->route->origin }} - {{ $stop->route->destination }}</td>
                                <td class="px-4 py-2 border">{{ $stop->stop_order }}</td>
                                <td class="px-4 py-2 border">{{ $stop->stop_name }}</td>
                                <td class="px-4 py-2 border">{{ $stop->travel_minutes ?? '-' }}</td>
                                <td class="px-4 py-2 border space-x-2">
                                    <a href="{{ route('stop.edit', $stop->id) }}" class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('stop.destroy', $stop->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-2 border text-center text-gray-500">Tidak ada data pemberhentian</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- Modal Tambah Harga --}}
<div id="modalAddPrice" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50 transition-opacity duration-300">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
        <h3 class="text-xl font-semibold mb-4">Tambah Harga Antar Titik</h3>
        <form action="{{ route('hargapertitik.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium">Rute</label>
                <select name="route_id" id="route_id" class="w-full border px-3 py-2 rounded">
                    @foreach ($routes as $route)
                        <option value="{{ $route->id }}">{{ $route->origin }} - {{ $route->destination }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium">Dari</label>
                <select name="from_stop_id" id="from_stop_id" class="w-full border px-3 py-2 rounded"></select>
            </div>
            <div>
                <label class="block text-sm font-medium">Ke</label>
                <select name="to_stop_id" id="to_stop_id" class="w-full border px-3 py-2 rounded"></select>
            </div>
            <div>
                <label class="block text-sm font-medium">Harga</label>
                <input type="number" name="price" class="w-full border px-3 py-2 rounded" required>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-400 text-white rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Harga Antar Titik -->
@foreach ($stopPrices as $price)
<div id="modalEditPrice{{ $price->id }}" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-lg">
        <h3 class="text-xl font-semibold mb-4">Edit Harga Antar Titik</h3>
        <form action="{{ route('hargapertitik.update', $price->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="route_id_{{ $price->id }}" class="block text-sm font-medium">Rute</label>
                <select name="route_id" id="route_id_{{ $price->id }}" class="w-full border px-3 py-2 rounded">
                    @foreach ($routes as $route)
                        <option value="{{ $route->id }}" @selected($price->route_id == $route->id)>
                            {{ $route->origin }} - {{ $route->destination }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="from_stop_id_{{ $price->id }}" class="block text-sm font-medium">Dari</label>
                <select name="from_stop_id" id="from_stop_id_{{ $price->id }}" class="w-full border px-3 py-2 rounded">
                    @foreach ($routeStops as $stop)
                        <option value="{{ $stop->id }}" @selected($price->from_stop_id == $stop->id)>
                            {{ $stop->stop_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="to_stop_id_{{ $price->id }}" class="block text-sm font-medium">Ke</label>
                <select name="to_stop_id" id="to_stop_id_{{ $price->id }}" class="w-full border px-3 py-2 rounded">
                    @foreach ($routeStops as $stop)
                        <option value="{{ $stop->id }}" @selected($price->to_stop_id == $stop->id)>
                            {{ $stop->stop_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="price_{{ $price->id }}" class="block text-sm font-medium">Harga</label>
                <input type="number" name="price" id="price_{{ $price->id }}"
                       value="{{ old('price',$price->price )}}"
                       class="w-full border px-3 py-2 rounded" required>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal('modalEditPrice{{ $price->id }}')" class="px-4 py-2 bg-gray-400 text-white rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endforeach


<script>
    function openModal() {
        document.getElementById('modalAddPrice').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('modalAddPrice').classList.add('hidden');
    }

    const allStops = @json($routeStops->groupBy('route_id'));
    const routeSelect = document.getElementById('route_id');
    const fromStopSelect = document.getElementById('from_stop_id');
    const toStopSelect = document.getElementById('to_stop_id');

    function populateStops(routeId) {
        const stops = allStops[routeId] || [];
        fromStopSelect.innerHTML = '';
        toStopSelect.innerHTML = '';
        stops.forEach(stop => {
            fromStopSelect.innerHTML += `<option value="${stop.id}">${stop.stop_name}</option>`;
            toStopSelect.innerHTML += `<option value="${stop.id}">${stop.stop_name}</option>`;
        });
    }
    window.addEventListener('DOMContentLoaded', () => populateStops(routeSelect.value));
    routeSelect.addEventListener('change', () => populateStops(routeSelect.value));
</script>
@endsection
