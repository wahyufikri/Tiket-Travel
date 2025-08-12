<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FonnteService;
use App\Models\Booking;
use Carbon\Carbon;

class SendTravelNotification extends Command
{
    protected $signature = 'send:travel-notification';
    protected $description = 'Kirim notifikasi WA otomatis ke pelanggan menjelang jadwal keberangkatan';

    protected $fonnte;

    public function __construct(FonnteService $fonnte)
    {
        parent::__construct();
        $this->fonnte = $fonnte;
    }

    public function handle()
    {
        $now = Carbon::now();

        $targetTimeStart = $now->copy()->subHour();
        $targetTimeEnd = $now->copy()->addHour();

        $bookings = Booking::whereHas('schedule', function ($query) use ($targetTimeStart, $targetTimeEnd) {
            // gabungkan tanggal dan waktu jadi datetime
            $query->whereRaw("STR_TO_DATE(CONCAT(departure_date, ' ', departure_time), '%Y-%m-%d %H:%i:%s') BETWEEN ? AND ?", [
                $targetTimeStart->format('Y-m-d H:i:s'),
                $targetTimeEnd->format('Y-m-d H:i:s')
            ]);
        })->with('schedule')->get();

        if ($bookings->isEmpty()) {
            $this->info("Tidak ada booking dengan jadwal keberangkatan dalam rentang waktu yang ditentukan.");
            return;
        }

        foreach ($bookings as $booking) {
            $departureDate = $booking->schedule->departure_date;
            $departureTime = $booking->schedule->departure_time;

            // Jika departure_time ternyata datetime lengkap, ambil hanya waktu HH:mm:ss nya saja:
            if (strlen($departureTime) > 8) {
                $departureTime = date('H:i:s', strtotime($departureTime));
            }

            $departureDateTime = $departureDate . ' ' . $departureTime;
            $departureTimeFormatted = Carbon::parse($departureDateTime)->format('d-m-Y H:i');

            $passengerName = $booking->passenger_name ?? 'Pelanggan';
            $phone = $booking->order->customer->phone ?? null;

            if (!$phone) {
                $this->error("Nomor telepon tidak tersedia untuk booking ID {$booking->id}");
                continue;
            }

            // Pastikan nomor dalam format internasional
            $phone = preg_replace('/[^0-9]/', '', $phone); // hilangkan karakter non-digit
            if (substr($phone, 0, 1) === '0') {
                $phone = '62' . substr($phone, 1);
            }

            $message = "Halo {$passengerName}, tiket travel Anda dengan jadwal keberangkatan pada {$departureTimeFormatted} akan berangkat segera. Mohon persiapkan diri. Terima kasih telah menggunakan AWR Travel.";

            $result = $this->fonnte->sendMessage($phone, $message);

            if (!empty($result['status']) && $result['status'] === true) {
                $this->info("Pesan terkirim ke {$phone}");
            } else {
                $this->error("Gagal mengirim pesan ke {$phone}");
                $this->line('Respons API: ' . json_encode($result));
            }
        }
    }
}
