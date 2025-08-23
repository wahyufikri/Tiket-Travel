<?php

use App\Http\Controllers\AutoScheduleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerLoginController;
use App\Http\Controllers\CustomerProfilController;
use App\Http\Controllers\CustomerRegisterController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManajemenAdminController;
use App\Http\Controllers\MidtransLogController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PublicScheduleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StopPriceController;
use App\Http\Controllers\StopsController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [PublicScheduleController::class, 'home'])->name('public.home');


Route::get('/dashboard', [DashboardAdminController::class, 'Dashboard'])->middleware('auth');
// Route::get('dshbrd-usr', [DashboardAdminController::class, 'index'])->middleware(['auth']);
// Route::resource('/dashboard-user', DashboardAdminController::class)->middleware(['auth']);


Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logoutadmin', [LoginController::class, 'logout']);

Route::post('/customer/login', [CustomerLoginController::class, 'login'])->name('customer.login');
Route::post('/customer/logout', [CustomerLoginController::class, 'logout'])->name('customer.logout');
Route::post('/customer/register', [CustomerRegisterController::class, 'register'])->name('customer.register');
Route::get('/test-snap', [PaymentController::class, 'testSnap']);







Route::resource('/manajemen-admin', ManajemenAdminController::class);

Route::resource('/manajemen-role', RoleController::class);

Route::resource('/sopir', DriverController::class)->middleware('auth');

Route::resource('/kendaraan', VehicleController::class)->middleware('auth');

Route::resource('/rute', RouteController::class)->middleware('auth');

Route::resource('/jadwal', ScheduleController::class)->middleware('auth');

Route::resource('/auto_schedule', AutoScheduleController::class);

Route::resource('/pemesanan', OrderController::class);

Route::get('/pemesanan/create/{route_id}', [OrderController::class, 'create']);



Route::resource('/pembayaran', PaymentController::class);
Route::resource('/stop', StopsController::class);
Route::resource('/pelanggan', CustomerController::class);



// web.php
Route::get('/stop/{stop}/edit', [StopsController::class, 'edit'])->name('stop.edit');

Route::resource('/hargapertitik', StopPriceController::class);

Route::prefix('keuangan')->name('keuangan.')->group(function () {
    // CRUD Transaksi
    Route::get('/', [TransactionController::class, 'index'])->name('index');
    Route::get('/create', [TransactionController::class, 'create'])->name('create');
    Route::post('/', [TransactionController::class, 'store'])->name('store');
    Route::get('/{transaction}/edit', [TransactionController::class, 'edit'])->name('edit');
    Route::put('/{transaction}', [TransactionController::class, 'update'])->name('update');
    Route::delete('/{transaction}', [TransactionController::class, 'destroy'])->name('destroy');

    // Kategori Transaksi
    Route::post('/categories', [TransactionController::class, 'storeCategory'])->name('categories.store');
    Route::delete('/categories/{id}', [TransactionController::class, 'destroyCategory'])->name('categories.destroy');

    // Metode Pembayaran
    Route::post('/payment-methods', [TransactionController::class, 'storePaymentMethod'])->name('payment-methods.store');
    Route::delete('/payment-methods/{id}', [TransactionController::class, 'destroyPaymentMethod'])->name('payment-methods.destroy');
});





// web.php
// Route::post('/checkout', [BookingController::class, 'checkout'])->name('checkout.show'); // proses dari pilih kursi ke halaman checkout
// Route::get('/checkout/{order}', [OrderController::class, 'show'])->name('checkout.payment'); // halaman pembayaran
// Route::post('/checkout/process', [BookingController::class, 'process'])->name('checkout.process'); // proses final pembayaran


Route::post('/checkout', [BookingController::class, 'checkout'])->name('checkout');
Route::get('/checkout/pay/{order}', [BookingController::class, 'pay'])->name('checkout.pay');



// Callback dari Midtrans (biar status order otomatis update)
Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle']);

// Optional: halaman sukses pembayaran
Route::get('/payment/success', function () {
    return view('homepage.public.success');
})->name('payment.success');




Route::get('/profil', [ProfilController::class, 'index'])->middleware('auth');

Route::get('/cari-jadwal', [PublicScheduleController::class, 'search'])->name('public.schedule');

Route::get('/booking', [BookingController::class, 'book'])->name('public.booking');
Route::get('/select-seat/{schedule_id}', [BookingController::class, 'showSeatSelection'])->name('public.seatSelection');
Route::post('/booking/seat-selection', [BookingController::class, 'selectSeat'])->name('public.processBooking')->middleware('auth:customer');











Route::post('/profil/update-password', [ProfilController::class, 'updatePassword'])->name('profil.updatePassword');




Route::middleware(['auth:customer'])->group(function () {
    Route::get('/profile', [CustomerProfilController::class, 'index'])->name('customer.profile');
    Route::get('/profile/edit', [CustomerProfilController::class, 'edit'])->name('customer.editProfile');
    Route::post('/profile/update', [CustomerProfilController::class, 'updateProfile'])->name('customer.updateProfile');
    Route::post('/logout', [CustomerProfilController::class, 'logout'])->name('customer.logout');
});


Route::post('/checkout/simulate-payment', [PaymentController::class, 'simulate'])->name('checkout.simulate');
Route::get('/checkout/success/{order}', [PaymentController::class, 'success'])->name('checkout.success');

Route::get('/orders/{order}/ticket', [OrderController::class, 'showTicket'])->name('orders.showTicket');
Route::get('/orders/{order}/download-ticket', [OrderController::class, 'downloadTicket'])->name('orders.downloadTicket');

Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle']);

Route::get('/get-price', function (Illuminate\Http\Request $request) {
    $price = \App\Models\StopPrice::where('from_stop_id', $request->origin_id)
        ->where('to_stop_id', $request->destination_id)
        ->value('price');

    return response()->json(['price' => $price ?? 0]);
});



Route::get('/midtrans/logs', [MidtransLogController::class, 'index'])->name('midtrans.logs');




Route::get('/get-seats/{schedule}', [OrderController::class, 'getSeats']);

Route::get('/keuangan/export/{type}/{month}/{year}', [TransactionController::class, 'exportPdf'])
    ->name('keuangan.export.pdf');
 // routes/web.php
Route::get('/checkout/success/{order_code}', [PaymentController::class, 'success'])
    ->name('checkout.success');


Route::get('/tentang-kami', function () {
    return view('homepage.public.about');
})->name('about');

// web.php
Route::post('/payment/snap-token/{order}', [BookingController::class, 'getSnapToken'])->name('payment.snap-token');

Route::get('/order/cetak', [OrderController::class, 'cetak'])->name('pemesanan.cetak');

Route::get('/cek-reservasi', [PublicScheduleController::class, 'index'])->name('cek-reservasi');




