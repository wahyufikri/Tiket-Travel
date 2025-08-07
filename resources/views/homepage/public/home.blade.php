@extends('homepage.layouts.main')

@section('title', 'Beranda')

@section('content')
<section class="bg-red-50 py-8">
    <div class="max-w-6xl mx-auto">
        <form action="{{ route('public.schedule') }}" method="GET"
              x-data="scheduleForm()" x-init="init()"
              class="bg-white shadow-md rounded-xl p-6 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

            {{-- Dari --}}
            <div>
    <label for="depart" class="block text-sm font-semibold text-gray-700 mb-1">Dari</label>
    <select id="depart" name="depart" required
            class="w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
        <option value="" disabled {{ request('depart') ? '' : 'selected' }}>Pilih asal</option>
        @foreach ($origins as $origin)
            <option value="{{ $origin->stop_name }}" {{ request('depart') == $origin->stop_name ? 'selected' : '' }}>
                {{ $origin->stop_name }}
            </option>
        @endforeach
    </select>
</div>


            {{-- Tujuan --}}
            <div>
    <label for="arrival" class="block text-sm font-semibold text-gray-700 mb-1">Tujuan</label>
    <select id="arrival" name="arrival" required
            class="w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
        <option value="" disabled {{ request('arrival') ? '' : 'selected' }}>Pilih tujuan</option>
        @foreach ($destinations as $destination)
            <option value="{{ $destination->stop_name }}" {{ request('arrival') == $destination->stop_name ? 'selected' : '' }}>
                {{ $destination->stop_name }}
            </option>
        @endforeach
    </select>
</div>

            {{-- Tanggal --}}
            <div>
                <label for="date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="date" id="date" value="{{ request('date') }}" required
                       class="w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
            </div>

            {{-- Penumpang --}}
            <div>
                <label for="pax" class="block text-sm font-semibold text-gray-700 mb-1">Penumpang</label>
                @php
                    $maxSeats = $schedules->min('available_seats') ?? 10;
                @endphp
                <input type="number" name="pax" id="pax" min="1" value="{{ request('pax', 1) }}"
                       required class="w-full border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500">
            </div>

            {{-- Tombol --}}
            <div>
                <button type="submit"
                        class="w-full bg-red-600 text-white font-semibold py-2 px-4 rounded-md hover:bg-red-700 transition duration-200">
                    Cek Jadwal
                </button>
            </div>
        </form>
    </div>
</section>

<section class="max-w-6xl mx-auto mt-10">
    <h2 class="text-xl font-bold mb-3">Pesanan Terakhir</h2>
    <div class="bg-gray-100 text-center p-4 rounded">
        Tidak ada pesanan terakhir
    </div>
</section>

{{-- Alpine Component --}}
{{-- <script>
    function scheduleForm() {
        return {
            selectedOrigin: '{{ request('depart') }}',
            routeStops: @json($routeStops),
            destinations: [],

            init() {
                this.updateDestinations();
            },

            updateDestinations() {
                const selected = this.routeStops.find(r => r.stop_name === this.selectedOrigin);
                if (!selected) {
                    this.destinations = [];
                    return;
                }

                this.destinations = this.routeStops.filter(r =>
                    r.route_id === selected.route_id && r.stop_order > selected.stop_order
                );
            },

            uniqueOrigins() {
                const origins = [];
                const seen = new Set();
                for (const stop of this.routeStops) {
                    if (!seen.has(stop.stop_name)) {
                        seen.add(stop.stop_name);
                        origins.push(stop.stop_name);
                    }
                }
                return origins;
            }
        }
    }
    function stopSelector() {
        return {
            routeStops: @json($routeStops),
            selectedOrigin: '',
            destinations: [],
            updateDestinations() {
    const matchingStops = this.routeStops.filter(r => r.stop_name === this.selectedOrigin);

    if (matchingStops.length === 0) {
        this.destinations = [];
        return;
    }

    // Ambil semua tujuan potensial dari semua rute yang punya asal tersebut
    let destSet = new Set();

    for (const stop of matchingStops) {
        const dests = this.routeStops.filter(r =>
            r.route_id === stop.route_id && r.stop_order > stop.stop_order
        );

        for (const d of dests) {
            destSet.add(JSON.stringify({ id: d.id, stop_name: d.stop_name }));
        }
    }

    // Konversi Set ke Array of object
    this.destinations = Array.from(destSet).map(str => JSON.parse(str));
}

        };
    }
</script> --}}
@endsection
