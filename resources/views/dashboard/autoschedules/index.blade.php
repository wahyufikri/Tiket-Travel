@extends('dashboard.layouts.main')

@section('content')
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Daftar Jadwal Otomatis</h2>
            <a href="{{ route('auto_schedule.create') }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                + Jadwal
            </a>
        </div>

        @if (session('success'))
            <div id="success-alert"
                class="mb-4 flex items-center justify-between p-4 bg-orange-100 border border-orange-400 text-orange-800 rounded-lg shadow-sm transition-opacity duration-500 ease-in-out">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()"
                    class="text-green-600 hover:text-green-800 text-sm">&times;</button>
            </div>

            <script>
                setTimeout(() => {
                    const alert = document.getElementById('success-alert');
                    if (alert) {
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 500);
                    }
                }, 6000);
            </script>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700 uppercase">
                    <tr>
                        <th class="px-4 py-2">Rute</th>
                        <th class="px-4 py-2">Kendaraan</th>
                        <th class="px-4 py-2">Sopir</th>
                        <th class="px-4 py-2">Hari</th>
                        <th class="px-4 py-2">Waktu</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    @endphp

                    @foreach ($autoSchedules as $auto)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $auto->route->origin }} → {{ $auto->route->destination }}</td>
                            <td class="px-4 py-2">{{ $auto->vehicle->vehicle_name }}</td>
                            <td class="px-4 py-2">{{ $auto->driver->name }}</td>
                            <td class="px-4 py-2">{{ $namaHari[$auto->weekday] }}</td>
                            <td class="px-4 py-2">{{ $auto->departure_time }}</td>
                            <td class="px-4 py-2 capitalize">{{ $auto->status }}</td>
                            <td class="px-4 py-2 flex space-x-2">
                                <a href="{{ route('auto_schedule.edit', $auto) }}"
                                    class="text-yellow-500 hover:text-yellow-700">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('auto_schedule.destroy', $auto) }}" method="POST"
                                    onsubmit="return confirm('Yakin hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if ($autoSchedules->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-500">Belum ada jadwal otomatis</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    {{ $autoSchedules->links() }}
@endsection
