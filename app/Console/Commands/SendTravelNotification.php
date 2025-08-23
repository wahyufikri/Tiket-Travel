<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FonnteService;
use App\Models\Booking;
use Carbon\Carbon;

class SendTravelNotification extends Command
{
    protected $signature = 'send:travel-notification {--test : Mode uji kirim semua booking tanpa cek waktu keberangkatan}';
    protected $description = 'Kirim notifikasi WA otomatis ke pelanggan menjelang jadwal keberangkatan';

    protected $fonnte;

    public function __construct(FonnteService $fonnte)
    {
        parent::__construct();
        $this->fonnte = $fonnte;
    }

    public function handle()
    {
        $isTestMode = $this->option('test');

        if ($isTestMode) {
            $this->info("🔹 Mode TEST aktif - semua booking akan dikirim WA tanpa cek waktu keberangkatan.");
            $bookings = Booking::with(['schedule', 'order.customer'])->get();
            $this->info("Booking ketemu (TEST): " . $bookings->count());
        } else {
            $now = now();
            $targetTimeStart = $now->copy()->addHours(2)->startOfMinute();
            $targetTimeEnd   = $now->copy()->addHours(2)->endOfMinute();

            $bookings = Booking::whereHas('schedule', function ($query) use ($targetTimeStart, $targetTimeEnd) {
                $query->whereRaw(
                    "CAST(CONCAT(departure_date, ' ', departure_time) AS DATETIME) BETWEEN ? AND ?",
                    [$targetTimeStart->format('Y-m-d H:i:s'), $targetTimeEnd->format('Y-m-d H:i:s')]
                );
            })
                ->with(['schedule', 'order.customer'])
                ->get();

            $this->info("Sekarang: " . $now->format('Y-m-d H:i:s'));
            $this->info("Target Start: " . $targetTimeStart->format('Y-m-d H:i:s'));
            $this->info("Target End: " . $targetTimeEnd->format('Y-m-d H:i:s'));
            $this->info("Booking ketemu: " . $bookings->count());
        }

        if ($bookings->isEmpty()) {
            $this->info("Tidak ada booking yang sesuai.");
            return;
        }


        foreach ($bookings as $booking) {
            $departureDate = $booking->schedule->departure_date;
            $departureTime = $booking->schedule->departure_time;

            // Cegah double tanggal
            if (strpos($departureTime, ' ') !== false) {
                $departureDateTime = Carbon::parse($departureTime);
            } else {
                $departureDateTime = Carbon::parse("$departureDate $departureTime");
            }

            $departureTimeFormatted = $departureDateTime->format('d-m-Y H:i');

            $passengerName = $booking->passenger_name ?? 'Pelanggan';
            $phone = $booking->order->customer->phone ?? null;

            if (!$phone) {
                $this->error("Nomor telepon tidak tersedia untuk booking ID {$booking->id}");
                continue;
            }

            // Format nomor WA internasional
            $phone = preg_replace('/[^0-9]/', '', $phone);
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
