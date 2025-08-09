@extends('dashboard.layouts.main')

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Edit Kendaraan</h2>
        @if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


        <form id="vehicleForm" action="{{ route('kendaraan.update', $vehicles->id) }}" method="POST"
            class="bg-white p-6 rounded-lg shadow-md space-y-4">
            @csrf
            @method('PUT')

            {{-- Nama kendaraan --}}
            <div>
                <label for="vehicle_name" class="block font-semibold">Nama Kendaraan</label>
                <input type="text" name="vehicle_name" id="vehicle_name" maxlength="60"
                    class="w-full border rounded px-3 py-2 mt-1" value="{{ old('vehicle_name', $vehicles->vehicle_name) }}"
                    required>
            </div>

            {{-- No Plat --}}
            <div>
                <label for="license_plate" class="block font-semibold">No Plat</label>
                <input type="text" name="license_plate" id="license_plate" maxlength="60"
                    class="w-full border rounded px-3 py-2 mt-1"
                    value="{{ old('license_plate', $vehicles->license_plate) }}" required>
            </div>

            {{-- Jenis --}}
            <div>
                <label for="type" class="block font-semibold">Jenis</label>
                <input type="text" name="type" id="type" maxlength="60"
                    class="w-full border rounded px-3 py-2 mt-1" value="{{ old('type', $vehicles->type) }}" required>
            </div>

            {{-- Warna --}}
            <div>
                <label for="color" class="block font-semibold">Warna</label>
                <input type="text" name="color" id="color" maxlength="60"
                    class="w-full border rounded px-3 py-2 mt-1" value="{{ old('color', $vehicles->color) }}" required>
            </div>

            {{-- Kapasitas dan Konfigurasi Kursi --}}
            <div x-data="seatConfig()" x-init="initOld()">
                <label for="capacity" class="block font-semibold">Kapasitas</label>
                <input type="number" name="capacity" id="capacity" class="w-full border rounded px-3 py-2 mt-1"
                    x-model="capacity" required>

                <div class="mt-4">
                    <label class="block font-semibold">Konfigurasi Kursi</label>
                    <template x-for="(seats, row) in configuration" :key="row">
                        <div class="flex items-center mt-1" x-for="(seats, row) in configuration" :key="row">
                            <input type="text" maxlength="2"
                                class="w-12 border px-2 py-1 rounded mr-2 text-center font-bold" x-model="rowKeys[row]"
                                @input="updateKey(row)">
                            <input type="number" min="1" class="w-20 border px-2 py-1 rounded"
                                x-model.number="configuration[row]" @input="updateTotalSeats()">
                            <button type="button" class="ml-2 text-red-600 hover:text-red-800"
                                @click="removeRow(row)">✕</button>
                        </div>

                    </template>

                    <button type="button" class="mt-3 px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600"
                        @click="addRow()">+ Tambah Baris</button>

                    <div class="mt-2 text-sm text-gray-600">
                        Total kursi: <span x-text="totalSeats"></span> dari <span x-text="capacity"></span>
                    </div>

                    <template x-if="totalSeats > capacity">
                        <div class="text-red-500 text-sm mt-1">
                            Total kursi melebihi kapasitas kendaraan!
                        </div>
                    </template>

                    {{-- Hidden input untuk seat_configuration --}}
                    <input type="hidden" name="seat_configuration" x-bind:value="seatConfigString">
                </div>
            </div>

            {{-- Tahun --}}
            <div>
                <label for="year" class="block font-semibold">Tahun</label>
                <input type="text" name="year" id="year" maxlength="4"
                    class="w-full border rounded px-3 py-2 mt-1" value="{{ old('year', $vehicles->year) }}" required>
            </div>

            {{-- Status --}}
            <div>
                <label class="block font-semibold mb-1">Status</label>
                <div class="flex space-x-6">
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="active"
                            {{ old('status', $vehicles->status) == 'active' ? 'checked' : '' }}>
                        <span class="ml-2">Aktif</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="inactive"
                            {{ old('status', $vehicles->status) == 'inactive' ? 'checked' : '' }}>
                        <span class="ml-2">Tidak Aktif</span>
                    </label>
                </div>
            </div>
            {{-- <div class="mb-4">
                <label for="current_location" class="block text-sm font-medium text-gray-700">Lokasi Terkini</label>
                <input type="text" name="current_location" id="current_location"
                    value="{{ old('current_location', $vehicle->current_location ?? '') }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div> --}}


            {{-- Tombol --}}
            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('kendaraan.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</a>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Update</button>
            </div>
        </form>
    </div>

    <script>
        function seatConfig() {
            return {
                capacity: {{ old('capacity', $vehicles->capacity ?? 0) }},
                configuration: {},
                rowKeys: {},
                totalSeats: 0,

                initOld() {
                    const config = @json(old('seat_configuration', $vehicles->seat_configuration));
                    if (config) {
                        config.split(',').forEach(pair => {
                            const [row, count] = pair.split('=');
                            this.configuration[row] = parseInt(count);
                            this.rowKeys[row] = row;
                        });
                        this.updateTotalSeats();
                    }
                },

                generateConfiguration() {
                    this.configuration = {};
                    this.rowKeys = {};
                    let remaining = this.capacity;
                    let charCode = 65;
                    while (remaining > 0) {
                        let row = String.fromCharCode(charCode++);
                        let seatsInRow = remaining >= 4 ? 4 : remaining;
                        this.configuration[row] = seatsInRow;
                        this.rowKeys[row] = row;
                        remaining -= seatsInRow;
                    }
                    this.updateTotalSeats();
                },

                updateKey(oldKey) {
                    const newKey = this.rowKeys[oldKey].toUpperCase().trim();
                    if (!newKey || newKey === oldKey) return;
                    if (this.configuration[newKey]) return; // Hindari duplikat

                    this.configuration[newKey] = this.configuration[oldKey];
                    this.rowKeys[newKey] = newKey;

                    delete this.configuration[oldKey];
                    delete this.rowKeys[oldKey];
                },

                removeRow(row) {
                    delete this.configuration[row];
                    delete this.rowKeys[row];
                    this.updateTotalSeats();
                },

                addRow() {
                    let charCode = 65;
                    let newKey = '';
                    while (true) {
                        newKey = String.fromCharCode(charCode++);
                        if (!this.configuration[newKey]) break;
                    }
                    this.configuration[newKey] = 1;
                    this.rowKeys[newKey] = newKey;
                    this.updateTotalSeats();
                },

                updateTotalSeats() {
                    this.totalSeats = Object.values(this.configuration).reduce((a, b) => parseInt(a) + parseInt(b), 0);
                },

                get seatConfigString() {
                    return Object.entries(this.configuration)
                        .map(([row, count]) => `${row}=${count}`)
                        .join(',');
                }
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            const form = document.getElementById("vehicleForm");
            form?.addEventListener("submit", () => {
                const hiddenInput = document.getElementById("seat_configuration_hidden");
                const alpineComponent = document.querySelector('[x-data]').__x.$data;
                if (hiddenInput && alpineComponent) {
                    hiddenInput.value = alpineComponent.seatConfigString;
                }
            });
        });
    </script>
@endsection
