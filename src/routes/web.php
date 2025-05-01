<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemLikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\MyPage\MyPageController;
use App\Http\Controllers\MyPage\ProfileController;
use App\Http\Controllers\Auth\EmailVerificationController;

// 認証画面
Fortify::loginView(fn() => view('auth.login'));
Fortify::registerView(fn() => view('auth.register'));

Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');

// メール認証通知画面（登録後に表示されるページ）
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware(['auth'])->name('verification.notice');

// 認証リンクの再送信（/email/verification に送信されたPOST）
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('status', '認証メールを再送信しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// メールの確認リンクアクセス時の処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // email_verified_at が更新

    return auth()->user()->profile_completed
    ? redirect('/')
    : redirect('/mypage/profile/create'); // 認証後にリダイレクト先
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/switch-tab', [ItemController::class, 'switchTab'])->name('items.switchTab');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');
Route::post('/item/{item}/comment', [CommentController::class, 'store'])->name('comments.store');

// 認証済みユーザー向け
Route::middleware('auth')->group(function () {

    // 出品フォーム表示
    Route::get('/sell', [ItemController::class, 'create'])->name('sell.create');
    // 出品処理
    Route::post('/sell', [ItemController::class, 'store'])->name('sell.store');

    // いいね機能
    Route::post('/items/{item}/toggle-like', [ItemLikeController::class, 'toggle'])->name('items.toggleLike');

    // 購入フロー
    Route::prefix('purchase/{item}')->name('purchase.')->group(function () {
        Route::get('/',[PurchaseController::class, 'show'])->name('show');
        Route::post('/',[PurchaseController::class, 'store'])->name('store');
        Route::get('address/edit',[PurchaseController::class, 'editAddress'])->name('address.edit');
        Route::post('address/update',[PurchaseController::class, 'updateAddress'])->name('address.update');
    });

    // マイページ & プロフィール
    Route::prefix('mypage')->name('mypage.')->group(function () {

        // マイページ本体
        Route::get('/', [MyPageController::class, 'index'])->name('index');
        Route::get('switch-tab', [MyPageController::class, 'switchTab'])->name('switchTab');

        // プロフィール作成／編集
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('create', [ProfileController::class, 'create'])->name('create');
            Route::post('',      [ProfileController::class, 'store'])->name('store');
            Route::get('',    [ProfileController::class, 'edit'])->name('edit');
            Route::put('',       [ProfileController::class, 'update'])->name('update');
        });
    });
});
