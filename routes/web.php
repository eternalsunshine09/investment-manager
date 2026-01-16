<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    DashboardController,
    ProductController,
    TransactionController,
    ReportController,
    PortfolioController,
    AccountController,
    ToolController,
    CashFlowController,
    WatchlistController,
    GoalController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. REDIRECT HALAMAN UTAMA KE LOGIN ---
Route::get('/', function () {
    return redirect()->route('login');
});

// --- 2. GUEST ROUTES (Belum Login) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    
    // Reset Password
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// --- 3. PROTECTED ROUTES (Sudah Login) ---
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- A. MANAJEMEN AKUN ---
    Route::resource('accounts', AccountController::class);

    // --- B. CASH FLOW ---
    Route::post('/cashflow/import', [CashFlowController::class, 'import'])->name('cashflow.import');
    Route::resource('cashflow', CashFlowController::class)->except(['create', 'show']);

    // --- C. INVESTASI ---
    Route::resource('products', ProductController::class);
    Route::resource('transactions', TransactionController::class);

    // --- D. LAPORAN ---
    Route::prefix('laporan')->name('reports.')->group(function() {
        Route::get('/', [ReportController::class, 'index'])->name('index'); 
        Route::get('/export', [ReportController::class, 'export'])->name('export');
        Route::post('/import', [ReportController::class, 'import'])->name('import');
    });

    // --- E. PORTOFOLIO ---
    // Update: Saya pisahkan 'asset.summary' dari grup agar namanya persis seperti yang dicari sidebar
    Route::prefix('portfolio')->name('portfolio.')->group(function() {
        Route::get('/', [PortfolioController::class, 'index'])->name('index'); 
        Route::post('/update-prices', [PortfolioController::class, 'updatePrices'])->name('update');
    });

    // [FIX ERROR DISINI] Kita definisikan manual agar namanya 'asset.summary' (bukan portfolio.summary)
    Route::get('/portfolio/summary', [PortfolioController::class, 'assetSummary'])->name('asset.summary');

    // --- F. TOOLS ---
    Route::prefix('tools')->name('tools.')->group(function() {
        Route::get('/rebalancing', [ToolController::class, 'rebalance'])->name('rebalance');
        Route::post('/rebalancing', [ToolController::class, 'saveAllocation'])->name('rebalance.store');
        Route::get('/kalender', [ToolController::class, 'calendar'])->name('calendar');
        Route::post('/kalender', [ToolController::class, 'storeEvent'])->name('calendar.store');
        Route::delete('/kalender/{id}', [ToolController::class, 'destroyEvent'])->name('calendar.destroy');
    });

    // --- G. WATCHLIST & GOALS ---
    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist.index');
    Route::post('/watchlist', [WatchlistController::class, 'store'])->name('watchlist.store');
    Route::delete('/watchlist/{id}', [WatchlistController::class, 'destroy'])->name('watchlist.destroy');

    Route::resource('goals', GoalController::class)->only(['store', 'update', 'destroy']);
});