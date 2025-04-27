<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CocktailsController;
use Illuminate\Support\Facades\Auth;

Route::get("/", [UserController::class, "viewLogin"])->name("viewLogin");
Route::get("/login", [UserController::class, "viewLogin"])->name("viewLogin");
Route::get("/register", [UserController::class, "viewRegister"])->name("register");
Route::post("/register", [UserController::class, "register"])->name("register");
Route::post("/login", [UserController::class, "login"])->name("login");

Route::middleware(['auth'])->group(function () {
    Route::get('/cocktails/saved', [CocktailsController::class, 'saved'])->name('cocktails.saved');
    Route::get('/cocktails', [CocktailsController::class, 'index'])->name('cocktails.index');
    Route::post('/cocktails/store', [CocktailsController::class, 'store']);
    Route::get('/cocktails/{id}', [CocktailsController::class, 'show'])->name('cocktails.show');
    Route::get('/cocktails/{id}/edit', [CocktailsController::class, 'edit'])->name('cocktails.edit');
    Route::put('/cocktails/{id}', [CocktailsController::class, 'update']);
    Route::delete('/cocktails/{id}', [CocktailsController::class, 'destroy']);
});

Route::middleware(['auth'])->post('logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

