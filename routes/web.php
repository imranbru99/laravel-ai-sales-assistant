<?php

use App\Http\Controllers\Public\DemoLoginController;
use App\Http\Controllers\Public\FeatureController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PricingController;
use App\Http\Controllers\Public\SolutionController;
use Illuminate\Support\Facades\Route;

// Public Marketing Website
Route::get('/', HomeController::class)->name('home');
Route::get('/features', [FeatureController::class, 'index'])->name('features.index');
Route::get('/features/{slug}', [FeatureController::class, 'show'])->name('features.show');
Route::get('/solutions', [SolutionController::class, 'index'])->name('solutions.index');
Route::get('/solutions/{preset}', [SolutionController::class, 'show'])->name('solutions.show');
Route::get('/pricing', PricingController::class)->name('pricing');

// Demo Switcher
Route::get('/demo', [DemoLoginController::class, 'index'])->name('demo.index');
Route::post('/demo/login/{role}', [DemoLoginController::class, 'login'])->name('demo.login');
Route::get('/demo/login/{role}', [DemoLoginController::class, 'login']);
