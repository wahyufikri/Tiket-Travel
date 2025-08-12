@extends('dashboard.layouts.main') <!-- Ganti sesuai layout utama kamu -->

@section('content')
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Data rute</h2>
            <a href="/rute/create" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                + Rute Baru
            </a>
        </div>

        {{-- Alert Sukses --}}
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
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800 text-sm">&times;</button>
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

        {{-- Form Pencarian --}}
        <div class="mb-4">
            <form action="{{ url('/rute') }}" method="GET" class="flex items-center max-w-sm space-x-2">
                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 3a7.5 7.5 0 006.15 13.65z" />
                        </svg>
                    </span>
                    <input type="text" name="search" placeholder="Cari nama"
                           value="{{ request('search') }}"
                           class="bg-white w-full pl-10 pr-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <button type="submit"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-200">
                    Cari
                </button>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700 uppercase">
                    <tr>
                        <th class="px-4 py-2">No</th>
                        <th class="px-4 py-2">
                            <a href="{{ route('rute.index', array_merge(request()->query(), ['sort_by' => 'origin', 'sort_direction' => $sortBy === 'origin' && $sortDirection === 'asc' ? 'desc' : 'asc'])) }}"
                               class="flex items-center space-x-1">
                                <span>Asal</span>
                                @if ($sortBy === 'origin')
                                    <i class="fas {{ $sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }}"></i>
                                @else
                                    <i class="fas fa-sort text-gray-400"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-4 py-2">
                            <a href="{{ route('rute.index', array_merge(request()->query(), ['sort_by' => 'destination', 'sort_direction' => $sortBy === 'destination' && $sortDirection === 'asc' ? 'desc' : 'asc'])) }}"
                               class="flex items-center space-x-1">
                                <span>Tujuan</span>
                                @if ($sortBy === 'destination')
                                    <i class="fas {{ $sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }}"></i>
                                @else
                                    <i class="fas fa-sort text-gray-400"></i>
                                @endif
                            </a>
                        </th>

                        <th class="px-4 py-2">Durasi (menit)</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($routes as $index => $route)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $index + 1 }}</td>
                            <td class="px-4 py-2">{{ $route->origin }}</td>
                            <td class="px-4 py-2">{{ $route->destination }}</td>

                            <td class="px-4 py-2">{{ $route->duration_minutes }} menit</td>
                            <td class="px-4 py-2 flex space-x-2">
                                <a href="/rute/{{ $route->id }}/edit" class="text-yellow-500 hover:text-yellow-700">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="/rute/{{ $route->id }}" method="post" class="d-inline">
                                    @method('DELETE')
                                    @csrf
                                    <button type="button"
    class="fas fa-trash text-red-500 hover:text-red-700 btn-delete"
    data-id="{{ $route->id }}"></button>

                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $routes->links() }}
    </div>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            let routeId = this.getAttribute('data-id');

            Swal.fire({
                title: 'Hapus Data Rute?',
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
                    form.action = `/rute/${routeId}`;

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
