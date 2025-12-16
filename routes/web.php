<?php

use Illuminate\Support\Facades\Route;
use App\Services\GeminiService;
use App\Livewire\DetailProyek;
use App\Livewire\AllProyekInvoice;
use App\Livewire\AllProyekKwitansi;
use App\Livewire\MyTasks;
use App\Livewire\UiTab;
use App\Livewire\Dashboard;

Route::view('/', 'welcome');

Route::get('/dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

Route::get('/tasks', function () {
    return view('livewire.my-tasks');
})->middleware(['auth']);

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

Route::get('/proyek/{proyekId}', UiTab::class)->name('proyek.detail');

Route::get('/proyek-invoice/print/{id}', [AllProyekInvoice::class, 'printInvoice'])
     ->name('proyek-invoice.print');

Route::get('/proyek-kwitansi/print/{id}', [AllProyekKwitansi::class, 'printKwitansi'])
    ->name('proyek-kwitansi.print');

Route::get('/proyek/{id}/proposal-pdf', [DetailProyek::class, 'generateProposal'])
    ->name('proposal-proyek.pdf');