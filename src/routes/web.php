<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Fortify;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemLikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Fortify::loginView(fn () => view('auth.login'));
Fortify::registerView(fn () => view('auth.register'));

Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/switch-tab', [ItemController::class, 'switchTab'])->name('items.switchTab');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');
Route::post('/item/{item}/comment', [CommentController::class, 'store'])->name('comments.store');

//いいね機能
Route::post('/items/{item}/toggle-like', [ItemLikeController::class, 'toggle'])->middleware('auth');

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
