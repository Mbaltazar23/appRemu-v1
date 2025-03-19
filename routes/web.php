<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CostCenterController;
use App\Http\Controllers\FinancialIndicatorController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\LiquidationController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SustainerController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\WorkerImportController;
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
})->middleware('clearcache');

Route::get('form', function () {
    return view('form');
})->middleware('clearcache');

Route::get('/select-school', function () {
    return view('schools.schoolSelect');
})->name('schoolSelect')->middleware(['auth', 'clearcache']);

Route::post('/set-school-session', [HomeController::class, 'setSchoolSession'])->name('setSchoolSession')->middleware(['auth', 'clearcache']);

// Route to handle setting the school session
Auth::routes();

Route::middleware((['auth', 'check.school.session', 'clearcache']))->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
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

    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'check.school.session', 'clearcache'])->group(function () {
    /** ROUTES FOR WORKER MANAGEMENT */
    Route::post('workers-import', WorkerImportController::class)->name('workers.import');
    // Routes for creating contracts and printing contracts for workers who have been settled
    Route::get('workers/{worker}/contracts/create', [WorkerController::class, 'createContract'])->name('contracts.create');  // View the contract creation form
    Route::post('workers/{worker}/contracts', [WorkerController::class, 'storeContract'])->name('contracts.store');  // Store the contract
    Route::get('workers/{worker}/contracts/print', [WorkerController::class, 'printContract'])->name('contracts.print');  // Print the contract
    // Route to list settled workers (workers who have been terminated)
    Route::get('settlements', [WorkerController::class, 'settlements'])->name('workers.settlements');  // Display the list of settled workers
    // Route to delete the termination date of a worker
    Route::put('/workers/{worker}/remove-settlement-date', [WorkerController::class, 'removeSettlementDate'])->name('workers.removeSettlementDate');
    // Route to show the termination form (finiquito)
    Route::get('workers/{worker}/settle', [WorkerController::class, 'settle'])->name('workers.settle');  // Show termination form for a worker
    // Route to update the settlement (finiquito) date
    Route::put('workers/{worker}/settle', [WorkerController::class, 'updateSettlementDate'])->name('workers.updateSettle');  // Update settlement date for a worker
    // Routes for handling annexes to contracts (add/update/remove annexes)
    Route::get('workers/contracts/{worker}/annexes', [WorkerController::class, 'showAnnexes'])->name('contracts.showAnnexes');  // Show the annexes for a worker's contract
    Route::get('workers/contracts/{worker}/annexes/create', [WorkerController::class, 'createAnnex'])->name('contracts.createAnnex');  // Show the form to create a new annex
    Route::get('workers/contracts/{worker}/annexes/edit/{annex}', [WorkerController::class, 'editAnnex'])->name('contracts.editAnnex');  // Edit an existing annex
    Route::post('workers/contracts/{worker}/annexes', [WorkerController::class, 'storeAnnex'])->name('contracts.storeAnnex');  // Store a new annex
    Route::put('workers/contracts/{worker}/annexes/{annex}', [WorkerController::class, 'updateAnnex'])->name('contracts.updateAnnex');  // Update an annex
    Route::delete('workers/contracts/{worker}/annexes/{annex}', [WorkerController::class, 'deleteAnnex'])->name('contracts.deleteAnnex');  // Delete an annex

    /** ROUTES FOR INSURANCE (ASSOCIATING WORKERS TO INSURANCE) */
    Route::get('insurances/{insurance}/link-worker', [InsuranceController::class, 'linkWorker'])->name('insurances.link_worker');  // Link a worker to an insurance
    Route::post('insurances/{insurance}/attach-worker', [InsuranceController::class, 'attachWorker'])->name('insurances.attach_worker');  // Attach a worker to the selected insurance
    Route::post('insurances/setParameters', [InsuranceController::class, 'setParameters'])->name('insurances.setParameters');  // Set parameters for the insurance

    /** ROUTES FOR BONUSES */
    Route::get('bonuses/partials/{action}', [BonusController::class, 'handleAction'])->name('bonuses.partials.action');  // Handle a specific bonus action (e.g., create, update)
    Route::get('bonuses/partials/list', [BonusController::class, 'listBonuses'])->name('bonuses.partials.list');  // List all partial bonuses
    Route::post('bonuses/update/params', [BonusController::class, 'updateParams'])->name('bonuses.updateParams');  // Update bonus parameters
    Route::get('bonuses/{bonus}/workers', [BonusController::class, 'showWorkers'])->name('bonuses.workers');  // Show the workers for a specific bonus
    Route::post('bonuses/{bonus}/update-workers', [BonusController::class, 'updateWorkers'])->name('bonuses.update-workers');  // Update workers associated with a bonus
    Route::get('bonuses/partials/worker/{worker_id}', [BonusController::class, 'workers'])->name('bonuses.partials.worker');  // Get partial bonus info for a specific worker
    Route::post('bonuses/workers/update', [BonusController::class, 'updateBonusWorker'])->name('bonuses.updateBonus');  // Update bonus details for a worker

    /** ROUTES FOR ECONOMIC INDICATORS */
    Route::get('financial-indicators', [FinancialIndicatorController::class, 'index'])->name('financial-indicators.index');  // View the list of economic indicators
    Route::get('financial-indicators/show/{index}', [FinancialIndicatorController::class, 'show'])->name('financial-indicators.show');  // Show details for a specific economic indicator
    Route::get('api/financial-indicators/values', [FinancialIndicatorController::class, 'getValues']);  // Get the values for economic indicators
    Route::post('financial-indicators/modify', [FinancialIndicatorController::class, 'modify'])->name('financial-indicators.modify');  // Modify economic indicators

    /** ROUTES FOR LIQUIDATION ITEMS (TEMPLATES) */
    Route::get('templates/moveUp/{template}/{position}', [TemplateController::class, 'moveUp'])->name('templates.moveUp');  // Move a template item up in the list
    Route::get('templates/moveDown/{template}/{position}', [TemplateController::class, 'moveDown'])->name('templates.moveDown');  // Move a template item down in the list

    /** ROUTES FOR LIQUIDATION PROCESS (Liquidations) */
    Route::get('liquidations', [LiquidationController::class, 'index'])->name('liquidations.index');  // List all liquidations
    Route::get('liquidations/select-workers/{workerType}', [LiquidationController::class, 'selectWorkerType'])->name('liquidations.selectWorker');  // Select workers based on type
    Route::get('liquidations/worker-liquidation/{workerId}', [LiquidationController::class, 'workerLiquidation'])->name('liquidations.workerLiquidation');  // Show the liquidation details for a specific worker
    Route::get('liquidations/create/{workerId}', [LiquidationController::class, 'create'])->name('liquidations.create');  // Create a liquidation for a specific worker
    Route::post('liquidations/store/{worker}', [LiquidationController::class, 'store'])->name('liquidations.store');  // Store a new liquidation
    Route::delete('liquidations/delete/{liquidation}/{workerId}', [LiquidationController::class, 'destroy'])->name('liquidations.destroy');  // Delete a specific liquidation
    Route::get('liquidations/{id}/glosa', [LiquidationController::class, 'getGlosa'])->name('liquidations.getGlosa');  // Get the glosa for a liquidation
    Route::get('liquidations/printGlosas/{type}', [LiquidationController::class, 'printGlosas'])->name('liquidations.printGlosas');  // Print all glosas of a specific type

    /** ROUTES FOR GENERATING PREVISIONAL REPORTS */
    Route::get('reports/typeInsurance/{type}', [ReportController::class, 'typeInsurance'])->name('reports.type');  // View reports by insurance type
    Route::get('reports/generate/{typeInsurance}/{month}/{year}/{insurance}', [ReportController::class, 'generateReport'])->name('reports.generate');  // Generate a report for a specific insurance

    /** ROUTES TO VIEW DOCUMENTS (CERTIFICATES) */
    Route::get('certificates/view/{year}', [CertificateController::class, 'view'])->name('certificates.view');  // View certificates for a specific year
    Route::get('certificates/print/{year}', [CertificateController::class, 'printer'])->name('certificates.print');  // Print certificates for a specific year
});
