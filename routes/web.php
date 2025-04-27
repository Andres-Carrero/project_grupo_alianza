<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get("/", [UserController::class, "viewLogin"])->name("viewLogin");
Route::get("/register", [UserController::class, "viewRegister"])->name("register");
Route::get("/dashboard", [UserController::class, "viewDashboard"])->name("dashboard");
Route::post("/register", [UserController::class, "register"])->name("register");
Route::post("/login", [UserController::class, "login"])->name("login");

