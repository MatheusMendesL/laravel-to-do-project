<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Main;

Route::middleware('CheckLogout')->group(function () {
    Route::get('/login', [Main::class, 'login'])->name('login');
    Route::post('/login_submit', [Main::class, 'login_submit'])->name('login_submit');
});

Route::middleware('CheckLogin')->group(function () {
    Route::get('/', [Main::class, 'index'])->name('index');
    Route::get('/main', [Main::class, 'main'])->name('main');
    Route::get('/logout', [Main::class, 'logout'])->name('logout');
});
