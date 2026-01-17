<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\GradeController; 
use App\Http\Controllers\UserController;  
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Panele ról (widoki)
    Route::get('/admin', [DashboardController::class, 'admin'])->name('panel.admin');
    Route::get('/nauczyciel', [DashboardController::class, 'nauczyciel'])->name('panel.nauczyciel');
    Route::get('/rodzic', [DashboardController::class, 'rodzic'])->name('panel.rodzic');
    Route::get('/uczen', [DashboardController::class, 'uczen'])->name('panel.uczen');

    // Nauczyciele
    Route::post('/admin/teacher', [UserController::class, 'storeTeacher'])->name('admin.users.storeTeacher');
    Route::get('/admin/teacher/{id}/edit', [UserController::class, 'editTeacher'])->name('admin.users.editTeacher');
    Route::put('/admin/teacher/{id}', [UserController::class, 'updateTeacher'])->name('admin.users.updateTeacher');
    Route::delete('/admin/teacher/{id}', [UserController::class, 'destroyTeacher'])->name('admin.users.destroyTeacher');

    // Uczniowie
    Route::post('/admin/users/student', [UserController::class, 'storeStudent'])->name('admin.users.storeStudent');
    Route::get('/admin/users/student/{id}/edit', [UserController::class, 'editStudent'])->name('admin.users.editStudent');
    Route::put('/admin/users/student/{id}', [UserController::class, 'updateStudent'])->name('admin.users.updateStudent');
    Route::delete('/admin/users/student/{id}', [UserController::class, 'destroyStudent'])->name('admin.users.destroyStudent');


    // --- ZARZĄDZANIE OCENAMI (GradeController) ---
    Route::post('/nauczyciel/grade', [GradeController::class, 'store'])->name('nauczyciel.grades.store');
    Route::get('/nauczyciel/grade/{id}/edit', [GradeController::class, 'edit'])->name('nauczyciel.grades.edit');
    Route::put('/nauczyciel/grade/{id}', [GradeController::class, 'update'])->name('nauczyciel.grades.update');
    Route::delete('/nauczyciel/grade/{id}', [GradeController::class, 'destroy'])->name('nauczyciel.grades.destroy');



    // --- LOGIKA ADMINISTRACYJNA (Zostaje w DashboardController lub do wydzielenia później) ---
    Route::post('/admin/classes', [DashboardController::class, 'storeClass'])->name('admin.classes.store');
    Route::delete('/admin/classes/{class}', [DashboardController::class, 'destroyClass'])->name('admin.classes.destroy');
    Route::post('/admin/subjects', [DashboardController::class, 'storeSubject'])->name('admin.subjects.store');
    Route::delete('/admin/subjects/{subject}', [DashboardController::class, 'destroySubject'])->name('admin.subjects.destroy');
    Route::post('/admin/assignments', [DashboardController::class, 'storeAssignment'])->name('admin.assignments.store');


    // --- BEZPIECZEŃSTWO ---
    Route::put('/user/password', [PasswordController::class, 'update'])->name('user.password.update');

});

if (file_exists(__DIR__.'/settings.php')) {
    require __DIR__.'/settings.php';
}
