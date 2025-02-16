<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [ContactController::class, 'index']);
Route::group(['prefix' => 'contacts', 'as' => 'contacts.'], function () {
    Route::get('/', [ContactController::class, 'index'])->name('index');
    Route::post('import', [ContactController::class, 'import'])->name('import');
    Route::post('store', [ContactController::class, 'store'])->name('store');
    Route::delete('delete', [ContactController::class, 'delete'])->name('delete');
    Route::delete('bulk-delete', [ContactController::class, 'bulkDelete'])->name('bulk-delete');
    Route::get('edit', [ContactController::class, 'edit'])->name('edit');
    Route::post('update', [ContactController::class, 'update'])->name('update');
});
