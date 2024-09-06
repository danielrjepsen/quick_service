<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    });
});

Route::get('/register/{invitation}', [AccountController::class, 'teamInvitationsRegister'])->middleware('signed')->name('team-invitations.register');

Route::middleware(['auth:sanctum', 'verified', 'tenant'])->group(function () {
    Route::get('/dashboard', [AccountController::class, 'index'])->name('dashboard');
    Route::get('/settings', [AccountController::class, 'settings'])->name('settings');
    Route::put('/settings/organisation/billing', [OrganisationController::class, 'updateBilling'])->name('settings.organisation.billing.update');
    Route::put('/settings/organisation/two-factor', [OrganisationController::class, 'updateTwoFactor'])->name('settings.organisation.updateTwoFactor');
    Route::get('/billing', [BillingController::class, 'show'])->name('billing');
    Route::post('/billing', [BillingController::class, 'process'])->name('billing.process');
    Route::get('/service', [ServiceController::class, 'index'])->name('service.index');
    Route::post('/service', [ServiceController::class, 'store'])->name('service.store');
    Route::post('/service/toggle-service', [ServiceController::class, 'toggleService'])->name('service.toggleService');
    Route::post('/service/toggle-complete-all-asset', [ServiceController::class, 'toggleCompleteAllAsset'])->name('service.toggleCompleteAllAsset');
    Route::post('/service/add-new-service', [ServiceController::class, 'addNewService'])->name('service.addNewService');
    Route::post('/service/get-products/{asset}', [ServiceController::class, 'getProductsNotOnAsset'])->name('service.getProductsNotOnAsset');
    Route::post('/service/update-date', [ServiceController::class, 'updateDate'])->name('service.updateDate');
    Route::get('/product', [ProductController::class, 'index'])->name('product.index');
    Route::post('/product', [ProductController::class, 'store'])->name('product.store');
    Route::post('/product/add-to-parent', [ProductController::class, 'addProductToParent'])->name('product.addProductToParent');
    Route::put('/product/update-product', [ProductController::class, 'updateProduct'])->name('product.updateProduct');
});
