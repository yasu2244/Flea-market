<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Fortify;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemLikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\MyPageController;

Fortify::loginView(fn () => view('auth.login'));
Fortify::registerView(fn () => view('auth.register'));

Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/switch-tab', [ItemController::class, 'switchTab'])->name('items.switchTab');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');
Route::post('/item/{item}/comment', [CommentController::class, 'store'])->name('comments.store');

//　商品購入
Route::middleware(['auth'])->group(function () {
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/{item}/address/edit', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/{item}/address/update', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

});

//いいね機能
Route::post('/items/{item}/toggle-like', [ItemLikeController::class, 'toggle'])->middleware('auth');

Route::middleware(['auth'])->group(function () {

    // プロフィールの初回設定（初回ログイン時）
    Route::get('/profile/create', [ProfileController::class, 'create'])->name('profile.create');
    Route::post('/profile/store', [ProfileController::class, 'store'])->name('profile.store');

    // プロフィールの閲覧
    Route::get('/mypage', [MyPageController::class, 'index'])->name('mypage.index');
    // タブ切り替え用 AJAX
    Route::get('/mypage/switch-tab', [MyPageController::class, 'switchTab'])->name('mypage.switchTab');
    // プロフィールの編集
    Route::get('mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

});
