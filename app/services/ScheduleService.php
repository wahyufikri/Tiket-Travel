<?php

namespace App\Services;

use App\Models\Route;
use App\Models\Schedule;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Seat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ScheduleService
{
    public static function createSchedule(array $data): Schedule
    {
        $departureDateTime = Carbon::createFromFormat('Y-m-d H:i', $data['departure_date'] . ' ' . $data['departure_time']);

        return DB::transaction(function () use ($data, $departureDateTime) {
            $vehicle = Vehicle::findOrFail($data['vehicle_id']);
            $driver = Driver::findOrFail($data['driver_id']);
            $route = Route::findOrFail($data['route_id']);

            // 1. Validasi lokasi terakhir driver
            $lastDriverSchedule = Schedule::where('driver_id', $driver->id)
                ->where(function ($q) use ($data) {
                    $q->where('departure_date', '<', $data['departure_date'])
                        ->orWhere(function ($q2) use ($data) {
                            $q2->where('departure_date', $data['departure_date'])
                                ->where('departure_time', '<=', $data['departure_time']);
                        });
                })
                ->orderByDesc('departure_date')
                ->orderByDesc('departure_time')
                ->first();

            if ($lastDriverSchedule && optional($lastDriverSchedule->route)->destination !== $route->origin) {
                throw ValidationException::withMessages([
                    'driver_id' => 'Sopir tidak berada di titik asal.'
                ]);
            }

            // 2. Validasi lokasi terakhir kendaraan
            $lastVehicleSchedule = Schedule::where('vehicle_id', $vehicle->id)
                ->where(function ($q) use ($data) {
                    $q->where('departure_date', '<', $data['departure_date'])
                        ->orWhere(function ($q2) use ($data) {
                            $q2->where('departure_date', $data['departure_date'])
                                ->where('departure_time', '<=', $data['departure_time']);
                        });
                })
                ->orderByDesc('departure_date')
                ->orderByDesc('departure_time')
                ->first();

            if ($lastVehicleSchedule && optional($lastVehicleSchedule->route)->destination !== $route->origin) {
                throw ValidationException::withMessages([
                    'vehicle_id' => 'Kendaraan tidak berada di titik asal.'
                ]);
            }

            // 3. Hitung waktu tiba
            $arrivalTime = $departureDateTime->copy()->addMinutes($route->duration_minutes);

            // 4. Buat jadwal
            $schedule = Schedule::create([
                'route_id' => $route->id,
                'vehicle_id' => $vehicle->id,
                'driver_id' => $driver->id,
                'departure_date' => $data['departure_date'],
                'departure_time' => $data['departure_time'],
                'arrival_time' => $arrivalTime->format('H:i'),
                'available_seats' => $vehicle->capacity ?? 0,
                'status' => $data['status'] ?? 'active',
            ]);

            // 5. Buat kursi
            $config = explode(',', $vehicle->seat_configuration);
            foreach ($config as $rowConfig) {
                if (!str_contains($rowConfig, '=')) continue;

                [$row, $count] = explode('=', $rowConfig);
                $row = strtoupper(trim($row));
                $count = (int) trim($count);

                if ($count <= 0) continue;

                for ($i = 1; $i <= $count; $i++) {
                    Seat::create([
                        'schedule_id' => $schedule->id,
                        'seat_number' => $row . $i,
                        'is_booked' => false,
                    ]);
                }
            }

            // 6. Update lokasi driver dan kendaraan
            $driver->current_location = $route->origin;
            $driver->save();

            $vehicle->current_location = $route->origin;
            $vehicle->save();

            return $schedule;
        });
    }
}
