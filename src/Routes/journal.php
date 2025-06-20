<?php

use Illuminate\Support\Facades\Route;
use Layman\LaravelJournal\Controllers\Auth\LoginController;
use Layman\LaravelJournal\Controllers\HomeController;

Route::middleware('web')->prefix('journal')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('journal.login');
    Route::post('login', [LoginController::class, 'login'])->name('journal.login');
    Route::post('logout', [LoginController::class, 'logout'])->name('journal.logout');
    Route::get('/home', [HomeController::class, 'index'])->name('journal.home');
});




