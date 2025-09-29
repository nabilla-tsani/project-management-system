<?php

use Illuminate\Support\Facades\Route;
use App\Services\GeminiService;
use App\Livewire\DetailProyek;
use App\Livewire\AllProyekInvoice;
use App\Livewire\AllProyekKwitansi;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

Route::get('/customer', function () {
    return view('customer');
})->middleware(['auth']);


Route::get('/proyek', function () {
    return view('proyek');
})->middleware(['auth'])
  ->name('proyek');

Route::get('/test-gemini', function (GeminiService $gemini) {
    return $gemini->chat('Halo Gemini, apakah kamu terbaca?');
});

Route::get('/proyek/{id}', DetailProyek::class)->name('proyek.detail');

Route::get('/proyek-invoice/print/{id}', [AllProyekInvoice::class, 'printInvoice'])
     ->name('proyek-invoice.print');

Route::get('/proyek-kwitansi/print/{id}', [AllProyekKwitansi::class, 'printKwitansi'])
    ->name('proyek-kwitansi.print');

Route::get('/proyek/{id}/proposal-pdf', [DetailProyek::class, 'generateProposal'])
    ->name('proposal-proyek.pdf');