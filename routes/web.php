<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Auth;

Route::get('/login', Login::class)->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/writing-coach', \App\Livewire\WritingCoach\Index::class)->name('writing-coach');
    Route::get('/vocabulary', \App\Livewire\Vocabulary\Index::class)->name('vocabulary');
    Route::get('/journal', \App\Livewire\Journal\Index::class)->name('journal');
    Route::get('/mistakes', \App\Livewire\Mistakes\Index::class)->name('mistakes');
    Route::get('/mistakes/practice', \App\Livewire\Mistakes\Practice::class)->name('mistakes.practice');
    Route::get('/grammar', \App\Livewire\Grammar\Index::class)->name('grammar');
    Route::get('/grammar/{id}', \App\Livewire\Grammar\Show::class)->name('grammar.show');
    Route::get('/grammar/{id}/quiz', \App\Livewire\Grammar\Quiz::class)->name('grammar.quiz');
    
    Route::get('/reading', \App\Livewire\Reading\Index::class)->name('reading');
    Route::get('/ai-reading', \App\Livewire\AIReading\Index::class)->name('ai-reading');
    Route::get('/reading/create', \App\Livewire\Reading\Form::class)->name('reading.create');
    Route::get('/reading/{id}/questions', \App\Livewire\Reading\QuestionsForm::class)->name('reading.questions.create');
    Route::get('/reading/{id}/practice', \App\Livewire\Reading\Practice::class)->name('reading.practice');
    Route::get('/reading/{id}/quiz', \App\Livewire\Reading\Quiz::class)->name('reading.quiz');
    
    Route::get('/timer', \App\Livewire\Timer\Index::class)->name('timer');
    Route::get('/timeline', \App\Livewire\Timeline\Index::class)->name('timeline');
    Route::get('/stats', \App\Livewire\Stats\Index::class)->name('stats');
    Route::get('/achievements', \App\Livewire\Achievements\Index::class)->name('achievements');
    
    // Auth logic
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});
