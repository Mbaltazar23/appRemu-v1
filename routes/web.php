<?php

use App\Http\Controllers\BonusController;
use App\Http\Controllers\FinancialIndicatorController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SustainerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkerController;
use Illuminate\Support\Facades\Auth;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('form', function () {
    return view('form');
});
Route::get('/select-school', function () {
    return view('schools.schoolSelect');
})->name('schoolSelect')->middleware('auth');

Route::post('/set-school-session', [\App\Http\Controllers\HomeController::class, 'setSchoolSession'])->name('setSchoolSession')->middleware('auth');

// Route to handle setting the school session
Auth::routes();

Route::middleware((['auth', 'check.school.session', 'clearcache']))->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('users', UserController::class);
    Route::resource('schools', SchoolController::class);
    Route::resource('sustainers', SustainerController::class);
    Route::resource('insurances', InsuranceController::class);
    Route::resource('bonuses', BonusController::class);
    Route::resource('workers', WorkerController::class);
    Route::resource('licenses', LicenseController::class);
    Route::resource('financial_indicators', FinancialIndicatorController::class);
    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

});

Route::middleware((['auth', 'check.school.session', 'clearcache']))->group(function () {

    /** RUTAS PARA WORKER  */
    //Rutas para la creacion del contrato
    Route::get('workers/{worker}/contracts/create', [WorkerController::class, 'createContract'])->name('contracts.create');
    // Ruta para crear contrato
    Route::post('workers/{worker}/contracts', [WorkerController::class, 'storeContract'])->name('contracts.store');
    // Ruta para imprimir el documento
    Route::get('workers/{worker}/contracts/print', [WorkerController::class, 'printContract'])->name('contracts.print');

    /** RUTAS PARA INSURANCE  (ASOCIAR WORKER AL INSURANCE)*/
    Route::get('/insurances/{insurance}/link-worker', [InsuranceController::class, 'linkWorker'])->name('insurances.link_worker');
    Route::post('/insurances/{insurance}/attach-worker', [InsuranceController::class, 'attachWorker'])->name('insurances.attach_worker');
    Route::get('/insurances/{worker}/{type}/parameters', [InsuranceController::class, 'getWorkerParameters']);

    /* RUTAS PARA BONUSES */
    Route::get('/bonuses/partials/list', [BonusController::class, 'list'])->name('bonuses.partials.list');
    Route::get('/bonuses/partials/params', [BonusController::class, 'generalParams'])->name('bonuses.partials.params');
    Route::get('/bonuses/partials/worker', [BonusController::class, 'workers'])->name('bonuses.partials.worker');
    Route::get('/bonuses/{bonus}/workers', [BonusController::class, 'showWorkers'])->name('bonuses.workers');
    Route::post('/bonuses/{bonus}/update-workers', [BonusController::class, 'updateWorkers'])->name('bonuses.update-workers');


    /** RUTAS PARA LOS INDICADORES ECONOMICOS */
    Route::get('financial-indicators', [FinancialIndicatorController::class, 'index'])->name('financial-indicators.index');
    Route::get('financial-indicators/show', [FinancialIndicatorController::class, 'show'])->name('financial-indicators.show');
	Route::post('financial-indicators/show', [FinancialIndicatorController::class, 'show'])->name('financial-indicators.show');
    Route::get('api/financial-indicators/values', [FinancialIndicatorController::class, 'getValues']);
    Route::post('financial-indicators/modify', [FinancialIndicatorController::class, 'modify'])->name('financial-indicators.modify');

});
