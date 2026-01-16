<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasswordController; // Dodano import
use Illuminate\Support\Facades\Route;

// Strona główna
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Grupa tras chronionych logowaniem
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Główny dashboard - punkt wejścia
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Panele ról
    Route::get('/admin', [DashboardController::class, 'admin'])->name('panel.admin');
    Route::get('/nauczyciel', [DashboardController::class, 'nauczyciel'])->name('panel.nauczyciel');
    Route::get('/rodzic', [DashboardController::class, 'rodzic'])->name('panel.rodzic');
    Route::get('/uczen', [DashboardController::class, 'uczen'])->name('panel.uczen');

    // --- AKCJE ADMINISTRATORA (Wszystko skierowane do DashboardController) ---
    
    // Zarządzanie klasami i przedmiotami
    Route::post('/admin/classes', [DashboardController::class, 'storeClass'])->name('admin.classes.store');
    Route::delete('/admin/classes/{class}', [DashboardController::class, 'destroyClass'])->name('admin.classes.destroy');
    Route::post('/admin/subjects', [DashboardController::class, 'storeSubject'])->name('admin.subjects.store');
    Route::delete('/admin/subjects/{subject}', [DashboardController::class, 'destroySubject'])->name('admin.subjects.destroy');

    // Zarządzanie Nauczycielami (Poprawiono: AdminController -> DashboardController)
    Route::post('/admin/teacher', [DashboardController::class, 'storeTeacher'])->name('admin.users.storeTeacher');
    Route::get('/admin/teacher/{id}/edit', [DashboardController::class, 'editTeacher'])->name('admin.users.editTeacher');
    Route::put('/admin/teacher/{id}', [DashboardController::class, 'updateTeacher'])->name('admin.users.updateTeacher');
    Route::delete('/admin/teacher/{id}', [DashboardController::class, 'destroyTeacher'])->name('admin.users.destroyTeacher');

    // Zarządzanie Uczniami i Rodzicami
    Route::post('/admin/users/student', [DashboardController::class, 'storeStudent'])->name('admin.users.storeStudent');
    Route::get('/admin/users/student/{id}/edit', [DashboardController::class, 'editStudent'])->name('admin.users.editStudent');
    Route::put('/admin/users/student/{id}', [DashboardController::class, 'updateStudent'])->name('admin.users.updateStudent');
    Route::delete('/admin/users/student/{id}', [DashboardController::class, 'destroyStudent'])->name('admin.users.destroyStudent');

    // Przypisania (nauczyciel-przedmiot-klasa)
    Route::post('/admin/assignments', [DashboardController::class, 'storeAssignment'])->name('admin.assignments.store');

    // --- AKCJE NAUCZYCIELA ---
    Route::post('/nauczyciel/grade', [DashboardController::class, 'storeGrade'])->name('nauczyciel.grades.store');
    Route::get('/nauczyciel/grade/{id}/edit', [DashboardController::class, 'editGrade'])->name('nauczyciel.grades.edit');
    Route::put('/nauczyciel/grade/{id}', [DashboardController::class, 'updateGrade'])->name('nauczyciel.grades.update');
    Route::delete('/nauczyciel/grade/{id}', [DashboardController::class, 'destroyGrade'])->name('nauczyciel.grades.destroy');

    // --- BEZPIECZEŃSTWO (Hasło) ---
    Route::put('/user/password', [PasswordController::class, 'update'])->name('user.password.update');

});

// Wczytywanie dodatkowych tras ustawień jeśli istnieją
if (file_exists(__DIR__.'/settings.php')) {
    require __DIR__.'/settings.php';
}
