<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LinkSPVController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PermissionsController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\RolesController;
use App\Http\Controllers\Auth\UsersController;
use App\Http\Controllers\Excel\ExcelController;
use App\Http\Controllers\Pdf\PdfController;
use App\Http\Controllers\Tasks\ClientHoursController;
use App\Http\Controllers\Tasks\ClientsController;
use App\Http\Controllers\Tasks\ClientTasksController;
use App\Http\Controllers\Tasks\FileDownloadController;
use App\Http\Controllers\Tasks\HoursController;
use App\Http\Controllers\Tasks\TasksController;
use App\Http\Controllers\Tasks\UserTasksController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('download', [FileDownloadController::class, 'download']);

Route::get('printable/{template}/{path}', [PdfController::class, 'get']);
Route::get('generate', [PdfController::class, 'generateColorBookPdf']);
Route::get('generatec', [PdfController::class, 'generateColorBookPdfCarta']);
Route::get('excel', [ExcelController::class, 'index']);

