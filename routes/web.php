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
Route::get('/preview', [GeneratorController::class, 'getPreview'])->name('preview.show');
Route::get('/preview-pdf', [GeneratorController::class, 'getPreviewPdf'])->name('preview.pdf');
Route::post('/save-config', [GeneratorController::class, 'saveConfig'])->name('config.save');
Route::get('/load-config', [GeneratorController::class, 'loadConfig'])->name('config.load');
Route::get('/generate/fonts', [GeneratorController::class, 'getFonts'])->name('fonts.list');
Route::post('/get-fonts-from-json', [GeneratorController::class, 'getFontsFromJson'])->name('fonts.from-json');
Route::get('/fonts.css', [GeneratorController::class, 'getFontCSS'])->name('fonts.css');


