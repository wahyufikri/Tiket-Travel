<?php

use App\Http\Controllers\AutoScheduleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerLoginController;
use App\Http\Controllers\CustomerProfilController;
use App\Http\Controllers\CustomerRegisterController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManajemenAdminController;
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
Route::post('/logout', [LoginController::class, 'logout']);

Route::post('/customer/login', [CustomerLoginController::class, 'login'])->name('customer.login');
Route::post('/customer/logout', [CustomerLoginController::class, 'logout'])->name('customer.logout');
Route::post('/customer/register', [CustomerRegisterController::class, 'register'])->name('customer.register');







Route::resource('/manajemen-admin', ManajemenAdminController::class);

Route::resource('/manajemen-role', RoleController::class);

Route::resource('/sopir', DriverController::class)->middleware('auth');

Route::resource('/kendaraan', VehicleController::class)->middleware('auth');

Route::resource('/rute', RouteController::class)->middleware('auth');

Route::resource('/jadwal', ScheduleController::class)->middleware('auth');

Route::resource('/auto_schedule', AutoScheduleController::class);

Route::resource('/pemesanan', OrderController::class);


Route::resource('/pembayaran', PaymentController::class);
Route::resource('/stop', StopsController::class);
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
Route::post('/checkout', [BookingController::class, 'checkout'])->name('checkout.show'); // proses dari pilih kursi ke halaman checkout
Route::get('/checkout/{order}', [OrderController::class, 'show'])->name('checkout.payment'); // halaman pembayaran
Route::post('/checkout/process', [BookingController::class, 'process'])->name('checkout.process'); // proses final pembayaran







Route::get('/profil', [ProfilController::class, 'index'])->middleware('auth');

Route::get('/cari-jadwal', [PublicScheduleController::class, 'search'])->name('public.schedule');

Route::get('/booking', [BookingController::class, 'book'])->name('public.booking');
Route::get('/select-seat/{schedule_id}', [BookingController::class, 'showSeatSelection'])->name('public.seatSelection');
Route::post('/booking/seat-selection', [BookingController::class, 'selectSeat'])->name('public.processBooking')->middleware('auth:customer');











Route::post('/profil/update-password', [ProfilController::class, 'updatePassword'])->name('profil.updatePassword');




Route::middleware(['auth:customer'])->group(function () {
    Route::get('/profile', [CustomerProfilController::class, 'index'])->name('customer.profile');
    Route::get('/profile/edit', [CustomerProfilController::class, 'edit'])->name('customer.editProfile');
    Route::post('/profile/update', [CustomerProfilController::class, 'update'])->name('customer.updateProfile');
    Route::post('/logout', [CustomerProfilController::class, 'logout'])->name('customer.logout');
});


Route::post('/checkout/simulate-payment', [PaymentController::class, 'simulate'])->name('checkout.simulate');
Route::get('/checkout/success/{order}', [PaymentController::class, 'success'])->name('checkout.success');

Route::get('/orders/{order}/ticket', [OrderController::class, 'showTicket'])->name('orders.showTicket');
Route::get('/orders/{order}/download-ticket', [OrderController::class, 'downloadTicket'])->name('orders.downloadTicket');


