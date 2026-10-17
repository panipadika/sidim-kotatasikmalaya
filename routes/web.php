<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AnalisisController;
use App\Http\Controllers\CapaianController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IndikatorController;
use App\Http\Controllers\PublicAuthController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\UserController;
use App\Services\AdminLoginPathService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

$adminLoginPath = AdminLoginPathService::getPath();

Route::middleware('guest:admin')->group(function () use ($adminLoginPath) {
    Route::get('/' . $adminLoginPath, [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/' . $adminLoginPath, [AdminAuthController::class, 'login'])->name('login.submit');
});

Route::middleware('guest:web')->group(function () {
    Route::post('/public/register', [PublicAuthController::class, 'register'])->name('public.register.submit');
});

Route::get('/public/login', [PublicAuthController::class, 'showLogin'])->name('public.login');
Route::post('/public/login', [PublicAuthController::class, 'login'])->name('public.login.submit');
Route::get('/public/register', [PublicAuthController::class, 'showRegister'])->name('public.register');

Route::middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analisis', [AnalisisController::class, 'index'])->name('analisis.index');
    Route::get('/detail-indikator/{indikator?}', [IndikatorController::class, 'publicDetail'])->name('detail.indikator');
    Route::resource('indikator', IndikatorController::class);
    Route::resource('capaian', CapaianController::class)->except(['show']);
    Route::resource('target', TargetController::class)->except(['show']);
    Route::get('/capaian/import', [CapaianController::class, 'showImport'])->name('capaian.import');
    Route::post('/capaian/import', [CapaianController::class, 'import'])->name('capaian.import.process');
    Route::get('/capaian/template', [CapaianController::class, 'template'])->name('capaian.template');
    Route::resource('users', UserController::class)->only(['index', 'destroy']);
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [AdminSettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/password', [AdminSettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::put('/settings/login-url', [AdminSettingsController::class, 'updateLoginPath'])->name('settings.login-url.update');
    Route::post('/settings/login-url/reset', [AdminSettingsController::class, 'resetLoginPath'])->name('settings.login-url.reset');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth:web,admin'])->group(function () {
    Route::get('/public/dashboard', [DashboardController::class, 'index'])->name('public.dashboard');
    Route::get('/public/analisis', [AnalisisController::class, 'index'])->name('public.analisis');
    Route::get('/public/detail-indikator/{indikator?}', [IndikatorController::class, 'publicDetail'])->name('public.detail.indikator');
    Route::get('/public/indikator/{indikator}', [IndikatorController::class, 'show'])->name('public.indikator.show');
    Route::post('/public/logout', [PublicAuthController::class, 'logout'])->name('public.logout');
});
