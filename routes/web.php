<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CostCenterController;
use App\Http\Controllers\FinancialIndicatorController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\LiquidationController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SustainerController;
use App\Http\Controllers\TemplateController;
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
})->name('schoolSelect')->middleware(['auth', 'clearcache']);

Route::post('/set-school-session', [\App\Http\Controllers\HomeController::class, 'setSchoolSession'])->name('setSchoolSession')->middleware(['auth', 'clearcache']);

// Route to handle setting the school session
Auth::routes();

Route::middleware((['auth', 'check.school.session', 'clearcache']))->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('schools', SchoolController::class);
    Route::resource('sustainers', SustainerController::class);
    Route::resource('insurances', InsuranceController::class);
    Route::resource('bonuses', BonusController::class);
    Route::resource('workers', WorkerController::class);
    Route::resource('licenses', LicenseController::class);
    Route::resource('absences', AbsenceController::class);
    Route::resource('templates', TemplateController::class);
    Route::resource('reports', ReportController::class);
    Route::resource('payrolls', PayrollController::class);
    Route::resource('certificates', CertificateController::class);
    Route::resource('costcenters', CostCenterController::class);
    Route::resource('historys', HistoryController::class);

    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware((['auth', 'check.school.session', 'clearcache']))->group(function () {
    /** RUTAS PARA WORKER  */
    //Rutas para la creacion del contrato e impresion e trabajadores finiquitados
    Route::get('workers/{worker}/contracts/create', [WorkerController::class, 'createContract'])->name('contracts.create');
    Route::post('workers/{worker}/contracts', [WorkerController::class, 'storeContract'])->name('contracts.store');
    Route::get('workers/{worker}/contracts/print', [WorkerController::class, 'printContract'])->name('contracts.print');
    // Ruta para listar trabajadores finiquitados
    Route::get('workers/settlement', [WorkerController::class, 'settlements'])->name('settlements.settlement');
    // Ruta para mostrar el formulario de finiquito
    Route::get('workers/{worker}/settle', [WorkerController::class, 'settle'])->name('workers.settle');
    // Ruta para actualizar la fecha de finiquito
    Route::put('workers/{worker}/settle', [WorkerController::class, 'updateSettlementDate'])->name('workers.updateSettle');
    // Ruta para ver y añadir y quitar los anexos a los contratos
    Route::get('workers/contracts/{worker}/annexes', [WorkerController::class, 'showAnnexes'])->name('contracts.showAnnexes');
    Route::get('workers/contracts/{worker}/annexes/create', [WorkerController::class, 'createAnnex'])->name('contracts.createAnnex');
    Route::get('workers/contracts/{worker}/annexes/edit/{annex}', [WorkerController::class, 'editAnnex'])->name('contracts.editAnnex');
    Route::post('workers/contracts/{worker}/annexes', [WorkerController::class, 'storeAnnex'])->name('contracts.storeAnnex');
    Route::put('workers/contracts/{worker}/annexes/{annex}', [WorkerController::class, 'updateAnnex'])->name('contracts.updateAnnex');
    Route::delete('workers/contracts/{worker}/annexes/{annex}', [WorkerController::class, 'deleteAnnex'])->name('contracts.deleteAnnex');

    /** RUTAS PARA INSURANCE  (ASOCIAR WORKER AL INSURANCE)*/
    Route::get('insurances/{insurance}/link-worker', [InsuranceController::class, 'linkWorker'])->name('insurances.link_worker');
    Route::post('insurances/{insurance}/attach-worker', [InsuranceController::class, 'attachWorker'])->name('insurances.attach_worker');
    Route::post('/insurances/setParameters', [InsuranceController::class, 'setParameters'])->name('insurances.setParameters');

    /* RUTAS PARA BONUSES */
    Route::get('bonuses/partials/{action}', [BonusController::class, 'handleAction'])->name('bonuses.partials.action');
    Route::get('bonuses/partials/list', [BonusController::class, 'list'])->name('bonuses.partials.list');
    Route::post('bonuses/update/params', [BonusController::class, 'updateParams'])->name('bonuses.updateParams');
    Route::get('bonuses/{bonus}/workers', [BonusController::class, 'showWorkers'])->name('bonuses.workers');
    Route::post('bonuses/{bonus}/update-workers', [BonusController::class, 'updateWorkers'])->name('bonuses.update-workers');
    Route::get('bonuses/partials/worker/{worker_id}', [BonusController::class, 'workers'])->name('bonuses.partials.worker');
    Route::post('bonuses/workers/update', [BonusController::class, 'updateBonusWorker'])->name('bonuses.updateBonus');

    /** RUTAS PARA LOS INDICADORES ECONOMICOS */
    Route::get('financial-indicators', [FinancialIndicatorController::class, 'index'])->name('financial-indicators.index');
    Route::get('financial-indicators/show/{index}', [FinancialIndicatorController::class, 'show'])->name('financial-indicators.show');
    Route::get('api/financial-indicators/values', [FinancialIndicatorController::class, 'getValues']);
    Route::post('financial-indicators/modify', [FinancialIndicatorController::class, 'modify'])->name('financial-indicators.modify');

    /** RUTAS PARA LOS ITEMS DE LIQUIDACION (Templates) */
    Route::get('templates/moveUp/{template}/{position}', [TemplateController::class, 'moveUp'])->name('templates.moveUp');
    Route::get('templates/moveDown/{template}/{position}', [TemplateController::class, 'moveDown'])->name('templates.moveDown');

    /** RUTAS PARA LA EMISION DE LIQUIDACIONES (Liquidations) */
    // Ruta GET para cargar los tipos de trabajador y procesar la selección
    Route::get('liquidations', [LiquidationController::class, 'index'])->name('liquidations.index');
    // Ruta GET para seleccionar un trabajador y su liquidación, pasando workerType como parámetro
    Route::get('liquidations/select-workers/{workerType}', [LiquidationController::class, 'selectWorkerType'])->name('liquidations.selectWorker');
    // Ruta GET para mostrar la liquidación de un trabajador específico
    Route::get('liquidations/worker-liquidation/{workerId}', [LiquidationController::class, 'workerLiquidation'])->name('liquidations.workerLiquidation');
    // Ruta GET para crear una liquidación, mantiene el workerId y workerType como parámetros
    Route::get('liquidations/create/{workerId}', [LiquidationController::class, 'create'])->name('liquidations.create');
    Route::post('liquidations/store/{worker}', [LiquidationController::class, 'store'])->name('liquidations.store');
    Route::delete('liquidations/delete/{liquidation}/{workerId}', [LiquidationController::class, 'destroy'])->name('liquidations.destroy');
    Route::get('liquidations/{id}/glosa', [LiquidationController::class, 'getGlosa'])->name('liquidations.getGlosa');
    Route::get('liquidations/printGlosas/{type}', [LiquidationController::class, 'printGlosas'])->name('liquidations.printGlosas');

    /** RUTAS PARA LA GENERACION DE INFORMES PREVISIONALES */
    Route::get('reports/typeInsurance/{type}', [ReportController::class, 'typeInsurance'])->name('reports.type');
    Route::get('reports/generate/{typeInsurance}/{month}/{year}/{insurance}', [ReportController::class, 'generateReport'])->name('reports.generate');

    /** RUTA PARA VISUALIZAR DOCUMENTOS (CERTIFICADOS) */
    Route::get('certificates/view/{year}', [CertificateController::class, 'view'])->name('certificates.view');
    Route::get('certificates/print/{year}', [CertificateController::class, 'print'])->name('certificates.print');
});
