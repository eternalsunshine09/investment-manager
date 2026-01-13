<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, DashboardController, ProductController, 
    TransactionController, ReportController, PortfolioController, 
    AccountController, ToolController, CashFlowController, WatchlistController, 
    GoalController, ConversionController, ForeignAccountController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. REDIRECT HALAMAN UTAMA ---
Route::get('/', function () {
    return redirect()->route('login');
});

// routes/web.php

// --- 2. GUEST ROUTES (Hanya untuk yang BELUM Login) ---
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // Register
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    
    // Lupa Password
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    
    // Reset Password
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// --- 3. PROTECTED ROUTES (Hanya untuk yang SUDAH Login) ---
Route::middleware(['auth'])->group(function () {

    // Proses Keluar (Logout)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // RESOURCE ROUTES (Produk, Rekening, Transaksi)
    Route::resource('products', ProductController::class);
    Route::resource('accounts', AccountController::class);
    Route::resource('transactions', TransactionController::class);

    // LAPORAN
    Route::get('/laporan-bulanan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/export', [ReportController::class, 'export'])->name('reports.export');
    Route::post('/laporan/import', [ReportController::class, 'import'])->name('reports.import');

    // PORTOFOLIO
    Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
    Route::get('/rekapan-aset', [PortfolioController::class, 'assetSummary'])->name('asset.summary');
    Route::post('/portfolio/update', [PortfolioController::class, 'updatePrices'])->name('portfolio.update');

    // TOOLS & KALENDER
    Route::get('/rebalancing', [ToolController::class, 'rebalance'])->name('tools.rebalance');
    Route::post('/rebalancing', [ToolController::class, 'saveAllocation'])->name('tools.rebalance.store');
    Route::get('/kalender', [ToolController::class, 'calendar'])->name('tools.calendar');
    Route::post('/kalender', [ToolController::class, 'storeEvent'])->name('tools.calendar.store');
    Route::delete('/kalender/{id}', [ToolController::class, 'destroyEvent'])->name('tools.calendar.destroy');

    // CASH FLOW
    Route::get('/cash-flow', [CashFlowController::class, 'index'])->name('cashflow.index');
    Route::post('/cash-flow', [CashFlowController::class, 'store'])->name('cashflow.store');
    Route::delete('/cash-flow/{id}', [CashFlowController::class, 'destroy'])->name('cashflow.destroy');

    // WATCHLIST
    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist.index');
    Route::post('/watchlist', [WatchlistController::class, 'store'])->name('watchlist.store');
    Route::delete('/watchlist/{id}', [WatchlistController::class, 'destroy'])->name('watchlist.destroy');

    // GOAL
    Route::post('/goals', [GoalController::class, 'store'])->name('goals.store');
    Route::put('/goals/{goal}', [GoalController::class, 'update'])->name('goals.update');
    Route::delete('/goals/{goal}', [GoalController::class, 'destroy'])->name('goals.destroy');

    // --- FITUR KONVERSI KURS ---
    Route::get('/conversion', [ConversionController::class, 'index'])->name('conversion.index');
    Route::post('/conversion', [ConversionController::class, 'store'])->name('conversion.store');

    // Route Khusus Valas
    Route::get('/foreign-accounts', [ForeignAccountController::class, 'index'])->name('foreign-accounts.index');
});