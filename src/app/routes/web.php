<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\AuthController;

// Гостевые страницы
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Защищенные страницы (нужен логин)
Route::middleware('auth')->group(function () {
    Route::get('/', [NoteController::class, 'index']);
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::put('/notes/{id}', [NoteController::class, 'update'])->name('notes.update');
    Route::get('/kanban', [NoteController::class, 'kanban'])->name('notes.kanban');
    Route::post('/notes/reorder', [NoteController::class, 'updateOrder'])->name('notes.reorder');
    Route::delete('/notes/{id}', [NoteController::class, 'destroy'])->name('notes.destroy');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
