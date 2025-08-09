<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AutoSchedule;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Seat;
use Carbon\Carbon;

class GenerateDailySchedule extends Command
{
    protected $signature = 'schedule:generate-daily';
    protected $description = 'Generate jadwal otomatis berdasarkan auto_schedules setiap hari';

    public function handle()
    {
        $tanggal = Carbon::now()->toDateString();
        $today = now()->dayOfWeek; // 0 = Minggu, 1 = Senin, dst.

        $autoSchedules = AutoSchedule::where('weekday', $today)->where('status', 'aktif')->get();

        foreach ($autoSchedules as $auto) {
            $route = $auto->route;
            $vehicle = $auto->vehicle;
            $driver = $auto->driver;

            if (!$route || !$vehicle || !$driver) {
                $this->warn("Data tidak lengkap untuk auto schedule ID {$auto->id}");
                continue;
            }

            // Cek apakah jadwal sudah dibuat
            $exists = Schedule::where([
                ['route_id', $route->id],
                ['vehicle_id', $vehicle->id],
                ['driver_id', $driver->id],
                ['departure_date', $tanggal],
                ['departure_time', $auto->departure_time],
            ])->exists();

            if ($exists) {
                $this->info("Jadwal sudah ada untuk: {$route->origin} - {$route->destination} pada $tanggal jam {$auto->departure_time}");
                continue;
            }

            $departureDateTime = Carbon::createFromFormat('Y-m-d H:i:s', "$tanggal {$auto->departure_time}");

            $arrivalTime = $departureDateTime->copy()->addMinutes($route->duration_minutes);

            $schedule = Schedule::create([
                'route_id' => $route->id,
                'vehicle_id' => $vehicle->id,
                'driver_id' => $driver->id,
                'departure_date' => $tanggal,
                'departure_time' => $auto->departure_time,
                'arrival_time' => $arrivalTime->format('H:i'),
                'available_seats' => $vehicle->capacity,
                'status' => 'active', // GANTI INI SESUAI ENUM DI MIGRASI
            ]);



            // Perbarui lokasi driver dan kendaraan
            $driver->current_location = $route->origin;
            $driver->save();

            $vehicle->current_location = $route->origin;
            $vehicle->save();

            $this->info("Jadwal berhasil dibuat: {$route->origin} ke {$route->destination} jam {$auto->departure_time}");
        }

        $this->info("Selesai generate jadwal otomatis untuk tanggal $tanggal.");
    }
}
