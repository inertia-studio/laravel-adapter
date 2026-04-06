<?php

use Illuminate\Support\Facades\Route;
use InertiaStudio\Laravel\Http\Controllers\ActionController;
use InertiaStudio\Laravel\Http\Controllers\AuthController;
use InertiaStudio\Laravel\Http\Controllers\FileUploadController;
use InertiaStudio\Laravel\Http\Controllers\FormStateController;
use InertiaStudio\Laravel\Http\Controllers\ModuleController;
use InertiaStudio\Laravel\Http\Controllers\PanelController;
use InertiaStudio\Laravel\Http\Controllers\RelationController;
use InertiaStudio\Laravel\Http\Controllers\SearchController;
use InertiaStudio\Laravel\Http\Middleware\Authenticate;
use InertiaStudio\Laravel\Http\Middleware\AuthorizePanelAccess;
use InertiaStudio\Laravel\PanelManager;

// Auth (no auth middleware)
Route::get('/login', [AuthController::class, 'showLogin'])->name('studio.login');
Route::post('/login', [AuthController::class, 'login'])->name('studio.login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('studio.logout');

// Registration
Route::get('/register', [AuthController::class, 'showRegister'])->name('studio.register');
Route::post('/register', [AuthController::class, 'register'])->name('studio.register.attempt');

// Password reset
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('studio.password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('studio.password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('studio.password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('studio.password.update');

// Protected routes
$guard = app(PanelManager::class)->getCurrentPanel()?->guard() ?? 'web';
Route::middleware([Authenticate::class.':'.$guard, AuthorizePanelAccess::class])->group(function () {
    // Email verification
    Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('studio.verification.notice');
    Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware('signed')->name('studio.verification.verify');
    Route::post('/verify-email/resend', [AuthController::class, 'resendVerification'])->middleware('throttle:6,1')->name('studio.verification.send');

    // Dashboard
    Route::get('/', [PanelController::class, 'dashboard'])->name('studio.dashboard');

    // Custom pages (auto-discovered from Pages/ directory)
    Route::get('/pages/{page}', [PanelController::class, 'customPage'])->name('studio.page');
    Route::match(['post', 'put', 'patch', 'delete'], '/pages/{page}/forms/{formKey}', [PanelController::class, 'handlePageForm'])->name('studio.page.form');
    Route::match(['post', 'put', 'patch', 'delete'], '/pages/{page}/actions/{actionKey}', [PanelController::class, 'handlePageAction'])->name('studio.page.action');

    // Profile
    Route::get('/profile', [PanelController::class, 'profile'])->name('studio.profile');
    Route::put('/profile', [PanelController::class, 'updateProfile'])->name('studio.profile.update');
    Route::put('/profile/password', [PanelController::class, 'updatePassword'])->name('studio.profile.password');

    // Search
    Route::get('/search', [SearchController::class, 'search'])->name('studio.search');

    // File uploads
    Route::post('/upload', [FileUploadController::class, 'upload'])->name('studio.upload');
    Route::delete('/upload', [FileUploadController::class, 'destroy'])->name('studio.upload.destroy');

    // Filter options search (server-side relationship search)
    Route::get('/{module}/filter-options/{filter}', [ModuleController::class, 'filterOptions'])->name('studio.filter-options');

    // Relationship field options search
    Route::get('/{module}/relation-options/{field}', [ModuleController::class, 'relationOptions'])->name('studio.relation-options');

    // Module CRUD — /{module} directly under panel path
    Route::get('/{module}', [ModuleController::class, 'index'])->name('studio.module.index');
    Route::get('/{module}/create', [ModuleController::class, 'create'])->name('studio.module.create');
    Route::post('/{module}', [ModuleController::class, 'store'])->name('studio.module.store');
    Route::get('/{module}/{record}', [ModuleController::class, 'show'])->name('studio.module.show');
    Route::get('/{module}/{record}/edit', [ModuleController::class, 'edit'])->name('studio.module.edit');
    Route::put('/{module}/{record}', [ModuleController::class, 'update'])->name('studio.module.update');
    Route::delete('/{module}/{record}', [ModuleController::class, 'destroy'])->name('studio.module.destroy');

    // Actions
    Route::post('/{module}/actions/{action}', [ActionController::class, 'execute'])->name('studio.actions.execute');
    Route::post('/{module}/bulk-actions/{action}', [ActionController::class, 'executeBulk'])->name('studio.actions.bulk');

    // Form reactivity
    Route::post('/{module}/form-state', [FormStateController::class, 'evaluate'])->name('studio.form-state');

    // Relations
    Route::get('/{module}/{record}/relations/{relation}', [RelationController::class, 'index'])->name('studio.relations.index');
});
