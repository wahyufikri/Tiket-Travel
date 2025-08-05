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
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PublicScheduleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\ScheduleController;
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

Route::get('/checkout/{order}', [OrderController::class, 'show'])->name('checkout.payment');
Route::post('/checkout/process', [BookingController::class, 'process'])->name('checkout.process');

Route::post('/checkout', [BookingController::class, 'checkout'])->name('checkout.show');





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

