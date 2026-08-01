<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Auth;

Route::get('/login', Login::class)->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/vocabulary', \App\Livewire\Vocabulary\Index::class)->name('vocabulary');
    Route::get('/journal', \App\Livewire\Journal\Index::class)->name('journal');
    Route::get('/mistakes', \App\Livewire\Mistakes\Index::class)->name('mistakes');
    
    // Auth logic
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});
