@extends('dashboard.layouts.main')

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Tambah Kendaraan Baru</h2>

        <form id="vehicleForm" action="{{ route('kendaraan.store') }}" method="POST"
            class="bg-white p-6 rounded-lg shadow-md space-y-4">
            @csrf
            <input type="hidden" name="seat_configuration" x-bind:value="seatConfigString">


            {{-- Pesan error global --}}


            {{-- Nama kendaraan --}}
            <div>
                <label for="vehicle_name" class="block font-semibold">Nama Kendaraan <span
                        class="text-red-500">*</span></label>
                <input type="text" name="vehicle_name" id="vehicle_name" maxlength="60"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    value="{{ old('vehicle_name') }}" placeholder="Masukkan Nama Kendaraan" required>
                @error('vehicle_name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- No Plat --}}
            <div>
                <label for="license_plate" class="block font-semibold">No Plat <span class="text-red-500">*</span></label>
                <input type="text" name="license_plate" id="license_plate" maxlength="60"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    value="{{ old('license_plate') }}" placeholder="Masukkan No Plat" required>
                @error('license_plate')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jenis --}}
            <div>
                <label for="type" class="block font-semibold">Jenis <span class="text-red-500">*</span></label>
                <input type="text" name="type" id="type" maxlength="60"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    value="{{ old('type') }}" placeholder="Masukkan Jenis Kendaraan" required>
                @error('type')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Warna --}}
            <div>
                <label for="color" class="block font-semibold">Warna <span class="text-red-500">*</span></label>
                <input type="text" name="color" id="color" maxlength="60"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    value="{{ old('color') }}" placeholder="Masukkan Warna" required>
                @error('color')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kapasitas dan Konfigurasi Kursi --}}
            {{-- Kapasitas dan Konfigurasi Kursi --}}
            <div x-data="seatConfig()" x-init="initOld()">
                <label for="capacity" class="block font-semibold">Kapasitas <span class="text-red-500">*</span></label>
                <input type="number" name="capacity" id="capacity"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    placeholder="Masukkan Kapasitas" x-model="capacity" required>
                @error('capacity')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror

                <div class="mt-4">
                    <label class="block font-semibold">Konfigurasi Kursi</label>
                    <template x-for="(seats, row) in configuration" :key="row">
                        <div class="flex items-center mt-1">
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

                    <!-- ✅ Tambahkan keterangan ini -->
                    <p class="mt-2 text-xs text-gray-500 italic">
                        Keterangan: A adalah barisan kursi paling depan, B barisan kedua, dan begitu seterusnya.
                    </p>

                    <div class="mt-2 text-sm text-gray-600">
                        Total kursi: <span x-text="totalSeats"></span> dari <span x-text="capacity"></span>
                    </div>

                    <template x-if="totalSeats > capacity">
                        <div class="text-red-500 text-sm mt-1">
                            Total kursi melebihi kapasitas kendaraan!
                        </div>
                    </template>

                    <input type="hidden" name="seat_configuration" id="seat_configuration_hidden"
                        x-bind:value="seatConfigString">
                </div>
            </div>


            {{-- Tahun --}}
            <div>
                <label for="year" class="block font-semibold">Tahun <span class="text-red-500">*</span></label>
                <input type="text" name="year" id="year" maxlength="4"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    value="{{ old('year') }}" placeholder="Masukkan Tahun Kendaraan" required>
                @error('year')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="block font-semibold mb-1">Status <span class="text-red-500">*</span></label>
                <div class="flex space-x-6">
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="active" class="form-radio text-red-500"
                            {{ old('status', 'active') == 'active' ? 'checked' : '' }}>
                        <span class="ml-2">Aktif</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="inactive" class="form-radio text-red-500"
                            {{ old('status') == 'inactive' ? 'checked' : '' }}>
                        <span class="ml-2">Tidak Aktif</span>
                    </label>
                </div>
                @error('status')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol --}}
            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('kendaraan.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</a>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Tambah</button>
            </div>
        </form>
    </div>

    {{-- Alpine.js --}}
    <script>
        function seatConfig() {
            return {
                capacity: {{ old('capacity', 0) }},
                configuration: {}, // { A: 3, B: 4, ... }
                rowKeys: {}, // { A: 'A', B: 'B', ... }
                totalSeats: 0,

                initOld() {
                    const oldConfig = @json(old('seat_configuration'));
                    if (oldConfig) {
                        let entries = oldConfig.split(',');
                        entries.forEach(e => {
                            let [row, count] = e.split('=');
                            this.configuration[row] = parseInt(count);
                            this.rowKeys[row] = row;
                        });
                        this.updateTotalSeats();
                    }
                },

                addRow() {
                    let newRow = this.generateNextRow();
                    this.configuration[newRow] = 1;
                    this.rowKeys[newRow] = newRow;
                    this.updateTotalSeats();
                },

                removeRow(row) {
                    delete this.configuration[row];
                    delete this.rowKeys[row];
                    this.updateTotalSeats();
                },

                updateKey(oldKey) {
                    const newKey = this.rowKeys[oldKey].toUpperCase();
                    if (newKey !== oldKey && newKey && !this.configuration[newKey]) {
                        this.configuration[newKey] = this.configuration[oldKey];
                        this.rowKeys[newKey] = newKey;
                        delete this.configuration[oldKey];
                        delete this.rowKeys[oldKey];
                    }
                    this.updateTotalSeats();
                },

                generateNextRow() {
                    const used = Object.keys(this.configuration);
                    for (let i = 65; i <= 90; i++) {
                        const row = String.fromCharCode(i);
                        if (!used.includes(row)) return row;
                    }
                    return 'X'; // fallback
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
            if (form) {
                form.addEventListener("submit", () => {
                    const hiddenInput = document.getElementById("seat_configuration_hidden");
                    const alpineComponent = document.querySelector('[x-data]').__x.$data;
                    if (hiddenInput && alpineComponent) {
                        hiddenInput.value = alpineComponent.seatConfigString;
                    }
                });
            }
        });
    </script>
@endsection
