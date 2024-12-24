<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarBookingController;

Route::redirect('', 'bookings', 301);
Route::resource('bookings', CarBookingController::class)->only(['index']);
