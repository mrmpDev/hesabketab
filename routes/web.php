<?php

use App\Http\Controllers\ExpensePdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('mrmp', function () {
    return 'salam';
});

Route::middleware(['web', 'auth'])->prefix('admin/expenses')->name('expenses.')->group(function () {
    Route::get('report.pdf', [ExpensePdfController::class, 'report'])->name('report.pdf');
    Route::get('{expense}/receipt.pdf', [ExpensePdfController::class, 'receipt'])->name('receipt.pdf');
});
