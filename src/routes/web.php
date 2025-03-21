<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Fortify;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;

Fortify::loginView(fn () => view('auth.login'));
Fortify::registerView(fn () => view('auth.register'));

Route::get('/', [ProductController::class, 'index'])->name('products.index');

Route::middleware(['auth'])->group(function () {

    // プロフィールの初回設定（初回ログイン時）
    Route::get('/profile/create', [ProfileController::class, 'create'])->name('profile.create');
    Route::post('/profile/store', [ProfileController::class, 'store'])->name('profile.store');

    // プロフィールの閲覧（ユーザー自身のプロフィール）
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

    // プロフィールの編集
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

});
