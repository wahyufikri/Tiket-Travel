@extends('dashboard.layouts.main')

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Tambah Sopir Baru</h2>
        

        <form action="{{ route('sopir.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4">
            @csrf

            <!-- Nama -->
            <div>
                <label for="name" class="block font-semibold">Nama <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" maxlength="60"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    placeholder="Masukkan Nama Pengguna" value="{{ old('name') }}" required>
            </div>

            <!-- No HP -->
            <div>
                <label for="phone_number" class="block font-semibold">No HP <span class="text-red-500">*</span></label>
                <input type="text" name="phone_number" id="phone_number" maxlength="60"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    placeholder="Masukkan No HP Sopir" value="{{ old('phone_number') }}" required>
            </div>

            <!-- Alamat -->
            <div>
                <label for="address" class="block font-semibold">Alamat <span class="text-red-500">*</span></label>
                <input type="text" name="address" id="address" maxlength="60"
                    class="w-full border rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                    placeholder="Masukkan Alamat Sopir" value="{{ old('address') }}" required>
            </div>

            <!-- Status -->
            <div>
                <label class="block font-semibold mb-1">Status <span class="text-red-500">*</span></label>
                <div class="flex space-x-6">
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="active" class="form-radio text-red-500" checked>
                        <span class="ml-2">Aktif</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="inactive" class="form-radio text-red-500">
                        <span class="ml-2">Tidak Aktif</span>
                    </label>
                </div>
            </div>

            <!-- Pilih Kendaraan -->
            <!-- Pilih Kendaraan -->
            <div x-data="vehicleDropdown()" class="relative w-full">
                <label class="block font-semibold mb-1">Pilih Kendaraan <span class="text-red-500">*</span></label>

                <!-- Tombol untuk buka dropdown -->
                <button type="button" @click="open = !open"
                    class="w-full border rounded px-3 py-2 text-left bg-white focus:outline-none focus:ring-2 focus:ring-red-500">
                    <template x-if="selected.length > 0">
                        <span x-text="selectedNames().join(', ')"></span>
                    </template>
                    <template x-if="selected.length === 0">
                        <span class="text-gray-400">Pilih Kendaraan</span>
                    </template>
                </button>

                <!-- Dropdown scrollable -->
                <div x-show="open" @click.away="open = false" x-transition
                    class="absolute z-50 bg-white border mt-1 w-full rounded shadow-lg max-h-[300px] overflow-y-auto">
                    <template x-for="vehicle in vehicles" :key="vehicle.id">
                        <label class="flex items-center space-x-2 px-3 py-2 hover:bg-gray-100">
                            <input type="checkbox" :value="vehicle.id" x-model="selected" class="form-checkbox">
                            <span x-text="vehicle.vehicle_name + ' - ' + vehicle.license_plate"></span>
                        </label>
                    </template>
                </div>

                <!-- Hidden inputs -->
                <template x-for="id in selected" :key="id">
                    <input type="hidden" name="vehicles[]" :value="id">
                </template>
            </div>


            <!-- Tombol -->
            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('sopir.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</a>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Tambah</button>
            </div>
        </form>
    </div>
    <script>
        function vehicleDropdown() {
            return {
                open: false,
                selected: [],
                vehicles: @json($vehicles),
                selectedNames() {
                    return this.vehicles
                        .filter(v => this.selected.includes(v.id))
                        .map(v => v.vehicle_name);
                }
            }
        }
    </script>
@endsection
