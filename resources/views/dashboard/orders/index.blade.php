@extends('dashboard.layouts.main')

@section('content')
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold">Manajemen Order</h2>
            <a href="/pemesanan/create" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                + Order
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

        <div class="mb-4">
            <form action="" method="GET" class="flex items-center max-w-sm space-x-2">
                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 3a7.5 7.5 0 006.15 13.65z" />
                        </svg>
                    </span>
                    <input type="text" name="search" placeholder="Cari Kode Order..." value="{{ request('search') }}"
                        class="bg-white w-full pl-10 pr-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <button type="submit"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-200">
                    Cari
                </button>
            </form>
        </div>

        <div class="flex items-center justify-between mb-4">
    <form action="{{ route('pemesanan.cetak') }}" method="GET" target="_blank"
        class="flex items-center gap-3 bg-white p-4 rounded-lg shadow-md">

        {{-- Pilih Filter --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Laporan</label>
            <select name="filter" id="filterSelect"
                class="border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="harian">Per Hari</option>
                <option value="bulanan">Per Bulan</option>
                <option value="tahunan">Per Tahun</option>
            </select>
        </div>

        {{-- Input Harian --}}
        <div id="inputHarian" class="hidden">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
            <input type="date" name="tanggal"
                class="border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500">
        </div>

        {{-- Input Bulanan --}}
        <div id="inputBulanan" class="hidden">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Bulan</label>
            <input type="month" name="bulan"
                class="border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500">
        </div>

        {{-- Input Tahunan --}}
        <div id="inputTahunan" class="hidden">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Tahun</label>
            <input type="number" name="tahun" placeholder="YYYY"
                class="border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500">
        </div>

        {{-- Tombol Cetak --}}
        <div class="flex items-end">
            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow transition-all duration-200 flex items-center gap-2">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
        </div>
    </form>
</div>


        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700 uppercase">
                    <tr>
                        <th class="px-4 py-2">No</th>
                        <th class="px-4 py-2">Kode Order</th>

                        <th class="px-4 py-2">Rute</th>
                        <th class="px-4 py-2">Nama Kursi</th>
                        <th class="px-4 py-2">Jumlah Tiket</th>
                        <th class="px-4 py-2">Total</th>
                        <th class="px-4 py-2">Status Order</th>
                        <th class="px-4 py-2">Status Bayar</th>
                        <th class="px-4 py-2">Diverifikasi Oleh</th>

                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $index => $order)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $index + $orders->firstItem() }}</td>
                            <td class="px-4 py-2">{{ $order->order_code }}</td>

                            <td class="px-4 py-2">
    {{ optional($order->booking->fromStop)->stop_name }} -> {{ optional($order->booking->toStop)->stop_name }}
</td>

                            <td class="px-4 py-2">
                                @foreach ($order->passengers as $p)
                                    {{ $p->seat_number }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </td>
                            <td class="px-4 py-2">{{ $order->seat_quantity }}</td>
                            <td class="px-4 py-2">Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td>@switch($order->order_status)
                                @case('menunggu')
                                    <span
                                        class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded-full">Menunggu</span>
                                @break

                                @case('proses')
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full">Diproses</span>
                                @break

                                @case('selesai')
                                    <span
                                        class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">Selesai</span>
                                @break

                                @case('batal')
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">Batal</span>
                                @break

                                @default
                                    <span
                                        class="bg-gray-100 text-gray-800 text-xs font-medium px-2 py-1 rounded-full">{{ $order->order_status }}</span>
                            @endswitch</td>

                            <td class="px-4 py-2 capitalize">
                                @switch($order->payment_status)
                                    @case('belum')
                                        <span class="bg-red-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded-full">Belum
                                            Bayar</span>
                                    @break

                                    @case('lunas')
                                        <span
                                            class="bg-yellow-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">Lunas</span>
                                    @break

                                    @case('gagal')
                                        <span
                                            class="bg-green-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">Gagal</span>
                                    @break

                                    @default
                                        <span
                                            class="bg-gray-100 text-gray-800 text-xs font-medium px-2 py-1 rounded-full">{{ $order->payment_status }}</span>
                                @endswitch
                            </td>
<td class="px-4 py-2">{{ $order->verifiedBy ? $order->verifiedBy->name : '-' }}</td>


                            <td class="px-4 py-2 flex space-x-2">
                                <a href="/pemesanan/{{ $order->id }}/edit" class="text-yellow-500 hover:text-yellow-700">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
        class="text-blue-500 hover:text-blue-700 btn-detail"
        data-order="{{ $order->toJson() }}">
        <i class="fas fa-eye"></i>
    </button>
                                <form action="/pemesanan/{{ $order->id }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
    class="fas fa-trash text-red-500 hover:text-red-700 btn-delete"
    data-id="{{ $order->id }}"></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-gray-500 py-4">Tidak ada data order</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <!-- Modal Detail -->
<!-- Modal Detail -->
<div id="detailModal"
     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-6 relative transform scale-95 transition-transform duration-300">

        <!-- Tombol Close -->
        <button id="closeModal"
                class="absolute top-3 right-3 text-gray-500 hover:text-red-500 transition-colors">
            <i class="fas fa-times text-xl"></i>
        </button>

        <!-- Header -->
        <div class="border-b pb-3 mb-4 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-ticket-alt text-red-600"></i>
                Detail Pemesanan
            </h2>
        </div>

        <!-- Konten -->
        <div id="modalContent" class="space-y-3 text-gray-700">
            {{-- Isi detail akan diisi lewat JS --}}
        </div>

        <!-- Footer -->

    </div>
</div>


            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        </div>
        <script>document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            let orderId = this.getAttribute('data-id');

            Swal.fire({
                title: 'Hapus Data Pemesanan?',
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
                    form.action = `/pemesanan/${orderId}`;

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

document.addEventListener('DOMContentLoaded', function () {
    const filterSelect = document.getElementById('filterSelect');
    const inputHarian = document.getElementById('inputHarian');
    const inputBulanan = document.getElementById('inputBulanan');
    const inputTahunan = document.getElementById('inputTahunan');

    function updateInputs() {
        inputHarian.classList.add('hidden');
        inputBulanan.classList.add('hidden');
        inputTahunan.classList.add('hidden');

        if (filterSelect.value === 'harian') {
            inputHarian.classList.remove('hidden');
        } else if (filterSelect.value === 'bulanan') {
            inputBulanan.classList.remove('hidden');
        } else if (filterSelect.value === 'tahunan') {
            inputTahunan.classList.remove('hidden');
        }
    }

    filterSelect.addEventListener('change', updateInputs);
    updateInputs(); // Set default saat load
});

document.addEventListener('DOMContentLoaded', function () {
    const detailModal = document.getElementById('detailModal');
    const modalContent = document.getElementById('modalContent');
    const closeModal = document.getElementById('closeModal');

    document.querySelectorAll('.btn-detail').forEach(button => {
        button.addEventListener('click', function () {
            let order = JSON.parse(this.getAttribute('data-order'));

            // isi detail ke modal
            modalContent.innerHTML = `
                <p><strong>Kode Order:</strong> ${order.order_code}</p>
                <p><strong>Nama Pemesan:</strong> ${order.customer.name}</p>
                <p><strong>No Telp:</strong> ${order.customer.phone}</p>
                <p><strong>Jumlah Tiket:</strong> ${order.seat_quantity}</p>
                <p><strong>Total Harga:</strong> Rp${order.total_price.toLocaleString()}</p>
                <p><strong>Status Order:</strong> ${order.order_status}</p>
                <p><strong>Status Bayar:</strong> ${order.payment_status}</p>
                <p><strong>Status Bayar:</strong> ${order.payment_status}</p>
            `;

            detailModal.classList.remove('hidden');
            detailModal.classList.add('flex');
        });
    });

    closeModal.addEventListener('click', function () {
        detailModal.classList.add('hidden');
        detailModal.classList.remove('flex');
    });
});
</script>
    @endsection
