<?php

use App\Http\Controllers\FileController;
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


Route::get('/', [FileController::class, 'index'])->name('index');
Route::post('upload', [FileController::class, 'upload'])->name('upload');
Route::post('encrypt', [FileController::class, 'encrypt'])->name('encrypt');
Route::post('decrypt', [FileController::class, 'decrypt'])->name('decrypt');
Route::get('/downloadEncrypted/{filename}', [FileController::class, 'downloadEncrypted'])->name('download.encrypted');
Route::get('/downloadDecrypted/{filename}', [FileController::class, 'downloadDecrypted'])->name('download.decrypted');