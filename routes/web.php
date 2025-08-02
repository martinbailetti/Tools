<?php

use App\Http\Controllers\UI\GeneratorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [GeneratorController::class, 'index'])->name('generator.index');
Route::post('/generate', [GeneratorController::class, 'generateColorBookPdf'])->name('pdf.generate');
