@extends('dashboard.layouts.main')

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-6">Manajemen Pemberhentian & Harga Rute</h2>

        <div class="flex flex-col lg:flex-row gap-6">

            {{-- Tabel Harga antar titik --}}
            <div class="w-full lg:w-1/2">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold">Daftar Harga Antar Titik</h3>
                    <button onclick="openModal()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Tambah Harga
                    </button>
                </div>
                <div class="bg-white shadow rounded-lg overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead class="bg-gray-100">
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
                                <tr>
                                    <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2 border">{{ $price->route->origin }} -
                                        {{ $price->route->destination }}</td>
                                    <td class="px-4 py-2 border">{{ $price->fromStop->stop_name }}</td>
                                    <td class="px-4 py-2 border">{{ $price->toStop->stop_name }}</td>
                                    <td class="px-4 py-2 border">Rp {{ number_format($price->price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 border space-x-2">
                                        <a href="{{ route('hargapertitik.edit', $price->id) }}"
                                            class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit</a>
                                        <form action="{{ route('hargapertitik.destroy', $price->id) }}" method="POST"
                                            class="inline-block" onsubmit="return confirm('Yakin hapus harga ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-2 border text-center">Tidak ada data harga</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tabel Pemberhentian Rute --}}
            <div class="w-full lg:w-1/2">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold">Daftar Pemberhentian Rute</h3>
                    <a href="{{ route('stop.create') }}"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Tambah Pemberhentian</a>
                </div>
                <div class="bg-white shadow rounded-lg overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Rute</th>
                                <th class="px-4 py-2 border">Urutan</th>
                                <th class="px-4 py-2 border">Nama Pemberhentian</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($routeStops as $stop)
                                <tr>
                                    <td class="px-4 py-2 border">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2 border">{{ $stop->route->origin }} -
                                        {{ $stop->route->destination }}</td>
                                    <td class="px-4 py-2 border">{{ $stop->stop_order }}</td>
                                    <td class="px-4 py-2 border">{{ $stop->stop_name }}</td>



                                    <td class="px-4 py-2 border space-x-2">
                                        <a href="{{ route('stop.edit', $stop->id) }}"
                                            class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit</a>
                                        <form action="{{ route('stop.destroy', $stop->id) }}" method="POST"
                                            class="inline-block" onsubmit="return confirm('Yakin hapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-2 border text-center">Tidak ada data pemberhentian
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <!-- Modal -->
    <div id="modalAddPrice" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-lg">
            <h3 class="text-xl font-semibold mb-4">Tambah Harga Antar Titik</h3>
            <form action="{{ route('hargapertitik.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="route_id" class="block text-sm font-medium">Rute</label>
                    <select name="route_id" id="route_id" class="w-full border px-3 py-2 rounded">
                        @foreach ($routes as $route)
                            <option value="{{ $route->id }}">{{ $route->origin }} - {{ $route->destination }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="from_stop_id" class="block text-sm font-medium">Dari</label>
                    <select name="from_stop_id" id="from_stop_id" class="w-full border px-3 py-2 rounded">
                        @foreach ($routeStops as $stop)
                            <option value="{{ $stop->id }}">{{ $stop->stop_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="to_stop_id" class="block text-sm font-medium">Ke</label>
                    <select name="to_stop_id" id="to_stop_id" class="w-full border px-3 py-2 rounded">
                        @foreach ($routeStops as $stop)
                            <option value="{{ $stop->id }}">{{ $stop->stop_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="price" class="block text-sm font-medium">Harga</label>
                    <input type="number" name="price" id="price" class="w-full border px-3 py-2 rounded" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 bg-gray-400 text-white rounded">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>
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

        // Kosongkan dulu
        fromStopSelect.innerHTML = '';
        toStopSelect.innerHTML = '';

        stops.forEach(stop => {
            const optionFrom = document.createElement('option');
            optionFrom.value = stop.id;
            optionFrom.textContent = stop.stop_name;

            const optionTo = document.createElement('option');
            optionTo.value = stop.id;
            optionTo.textContent = stop.stop_name;

            fromStopSelect.appendChild(optionFrom);
            toStopSelect.appendChild(optionTo);
        });
    }

    // Saat halaman pertama kali muncul
    window.addEventListener('DOMContentLoaded', () => {
        populateStops(routeSelect.value);
    });

    // Saat route berubah
    routeSelect.addEventListener('change', () => {
        populateStops(routeSelect.value);
    });
    </script>
@endsection
