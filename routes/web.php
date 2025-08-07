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
Route::post('/preview-pdf', [GeneratorController::class, 'getPreviewPdf'])->name('preview.pdf');
Route::post('/save-config', [GeneratorController::class, 'saveConfig'])->name('config.save');
Route::get('/load-config', [GeneratorController::class, 'loadConfig'])->name('config.load');
Route::get('/generate/fonts', [GeneratorController::class, 'getFonts'])->name('fonts.list');
Route::post('/get-fonts-from-json', [GeneratorController::class, 'getFontsFromJson'])->name('fonts.from-json');
Route::get('/fonts.css', [GeneratorController::class, 'getFontCSS'])->name('fonts.css');
Route::get('/test-excel', [GeneratorController::class, 'testExcelReading'])->name('test.excel');
Route::get('/list-sheets', [GeneratorController::class, 'listExcelSheets'])->name('list.sheets');
Route::get('/book', [GeneratorController::class, 'getBookByToken'])->name('book');
Route::get('/books', [GeneratorController::class, 'getBooks'])->name('books');

// Ruta para servir archivos PDF temporales
Route::get('/temp/{filename}', function ($filename) {
    $path = public_path('temp/' . $filename);

    // Verificar que el archivo existe y es un PDF
    if (!file_exists($path) || pathinfo($path, PATHINFO_EXTENSION) !== 'pdf') {
        abort(404);
    }

    return response()->file($path, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . $filename . '"'
    ]);
})->where('filename', '[A-Za-z0-9_\-\.]+')->name('temp.pdf');


