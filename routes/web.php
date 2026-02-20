<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AccountManagementController;
use App\Http\Controllers\Admin\InstitutionManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::redirect('/', '/dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/accounts', [AccountManagementController::class, 'index'])->name('admin.accounts.index');
        Route::get('/admin/accounts/create', [AccountManagementController::class, 'create'])->name('admin.accounts.create');
        Route::post('/admin/accounts', [AccountManagementController::class, 'store'])->name('admin.accounts.store');
        Route::get('/admin/accounts/{user}', [AccountManagementController::class, 'show'])->name('admin.accounts.show');
        Route::get('/admin/accounts/{user}/edit', [AccountManagementController::class, 'edit'])->name('admin.accounts.edit');
        Route::put('/admin/accounts/{user}', [AccountManagementController::class, 'update'])->name('admin.accounts.update');
        Route::delete('/admin/accounts/{user}', [AccountManagementController::class, 'destroy'])->name('admin.accounts.destroy');

        Route::get('/admin/instansi', [InstitutionManagementController::class, 'index'])->name('admin.instansi.index');
        Route::get('/admin/instansi/create', [InstitutionManagementController::class, 'create'])->name('admin.instansi.create');
        Route::post('/admin/instansi', [InstitutionManagementController::class, 'store'])->name('admin.instansi.store');
        Route::get('/admin/instansi/{instansi}', [InstitutionManagementController::class, 'show'])->name('admin.instansi.show');
        Route::get('/admin/instansi/{instansi}/edit', [InstitutionManagementController::class, 'edit'])->name('admin.instansi.edit');
        Route::put('/admin/instansi/{instansi}', [InstitutionManagementController::class, 'update'])->name('admin.instansi.update');
        Route::delete('/admin/instansi/{instansi}', [InstitutionManagementController::class, 'destroy'])->name('admin.instansi.destroy');
    });

    Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/submissions/{submission}', [SubmissionController::class, 'show'])
        ->whereNumber('submission')
        ->name('submissions.show');

    Route::middleware('role:operator_pemda')->group(function () {
        Route::get('/submissions/create', [SubmissionController::class, 'create'])->name('submissions.create');
        Route::post('/submissions', [SubmissionController::class, 'store'])->name('submissions.store');
        Route::get('/submissions/{submission}/edit', [SubmissionController::class, 'edit'])
            ->whereNumber('submission')
            ->name('submissions.edit');
        Route::put('/submissions/{submission}', [SubmissionController::class, 'update'])
            ->whereNumber('submission')
            ->name('submissions.update');
        Route::delete('/submissions/{submission}', [SubmissionController::class, 'destroy'])
            ->whereNumber('submission')
            ->name('submissions.destroy');
    });

    Route::middleware('role:operator_kanwil')->group(function () {
        Route::patch('/submissions/{submission}/status', [SubmissionController::class, 'updateStatus'])
            ->whereNumber('submission')
            ->name('submissions.update-status');
        Route::post('/submissions/{submission}/dispose', [SubmissionController::class, 'dispose'])
            ->whereNumber('submission')
            ->name('submissions.dispose');
    });

    Route::middleware('role:operator_kanwil,operator_divisi_p3h')->group(function () {
        Route::get('/submissions/{submission}/status-disposisi', [SubmissionController::class, 'statusDispositionForm'])
            ->whereNumber('submission')
            ->name('submissions.status-disposisi.form');
        Route::post('/submissions/{submission}/status-disposisi', [SubmissionController::class, 'saveStatusDisposition'])
            ->whereNumber('submission')
            ->name('submissions.status-disposisi.save');
    });

    Route::middleware('role:operator_divisi_p3h,kakanwil,kepala_divisi_p3h,analis_hukum')->group(function () {
        Route::post('/submissions/{submission}/result', [SubmissionController::class, 'uploadResult'])
            ->whereNumber('submission')
            ->name('submissions.upload-result');
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    });

    Route::middleware('role:operator_divisi_p3h,kakanwil,kepala_divisi_p3h')->group(function () {
        Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::get('/submissions/{submission}/penugasan', [AssignmentController::class, 'createFromSubmission'])
            ->whereNumber('submission')
            ->name('submissions.penugasan.form');
        Route::post('/submissions/{submission}/penugasan', [AssignmentController::class, 'storeFromSubmission'])
            ->whereNumber('submission')
            ->name('submissions.penugasan.save');
    });

    Route::middleware('role:analis_hukum,operator_divisi_p3h,operator_pemda')->group(function () {
        Route::get('/hasil-analisis', [AssignmentController::class, 'analysisResults'])
            ->name('assignments.analysis-results');
    });

    Route::middleware('role:analis_hukum')->group(function () {
        Route::patch('/assignments/{assignment}/status', [AssignmentController::class, 'updateStatus'])
            ->whereNumber('assignment')
            ->name('assignments.update-status');
        Route::post('/assignments/{assignment}/take', [AssignmentController::class, 'take'])
            ->whereNumber('assignment')
            ->name('assignments.take');
        Route::get('/assignments/{assignment}/upload-hasil', [AssignmentController::class, 'uploadAnalysisForm'])
            ->whereNumber('assignment')
            ->name('assignments.upload-hasil.form');
        Route::post('/assignments/{assignment}/upload-hasil', [AssignmentController::class, 'uploadAnalysisStore'])
            ->whereNumber('assignment')
            ->name('assignments.upload-hasil.store');
    });

    Route::middleware('role:operator_divisi_p3h,analis_hukum')->group(function () {
        Route::post('/assignments/{assignment}/document', [AssignmentController::class, 'uploadDocument'])
            ->whereNumber('assignment')
            ->name('assignments.upload-document');
    });
});
