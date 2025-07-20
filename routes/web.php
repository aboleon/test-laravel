<?php

use App\Http\Controllers\{AjaxController,
    AjaxPublicController,
    PDFController,
    GlobalExportController,
    PdfHubController,
    TestableController};
use App\Http\Controllers\Auth\AuthenticatedSessionController;
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
// Pour essayer des fonc.

require __DIR__.'/auth.php';


Route::group(['middleware' => 'auth.very_basic'], function () {
    Route::get('testable', [TestableController::class, 'index']);
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    include('front/routes.php');
    include('panel/routes.php');
    include('exceptions.php');
    include('dev.php');


// Ajax requests
    Route::match(['get', 'post'], 'ajax', [AjaxController::class, 'distribute'])->name('ajax');

    Route::post('webajax', [AjaxPublicController::class, 'distribute'])->name('webajax');

    Route::get('/pdf-print', [PdfHubController::class, 'printPdf'])->name('pdf_print');
    Route::get('pdf/{type}/{identifier}', [PDFController::class, 'distribute'])->name('pdf-printer');
    Route::post('/pdf-merged', [PDFController::class, 'streamMergedPdf']);
    Route::post('/pdf-global-export', [GlobalExportController::class, 'globalExport'])->name('pdf-globalExport');
});

Route::get('/dashboard', function () {
    return redirect('/panel/dashboard');
})->name('dashboard');
